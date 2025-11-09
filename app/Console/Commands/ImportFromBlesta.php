<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\ConfigOption;
use App\Models\CustomProperty;
use App\Models\Product;
use App\Models\Server;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Providers\SettingsProvider;
use Closure;
use DB;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Batch;
use Log;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class ImportFromBlesta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-from-blesta {dbname} {username?} {host?} {port?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from Blesta to Paymenter';

    /**
     * The PDO connection to Blesta database
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * @var int
     */
    protected $batchSize = 500;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set max memory to 1GB
        ini_set('memory_limit', '1024M');

        $dbname = $this->argument('dbname');
        $host = $this->askOrUseENV(argument: 'host', env: 'DB_HOST', question: 'Enter the host:', placeholder: 'localhost');
        $port = $this->askOrUseENV(argument: 'port', env: 'DB_PORT', question: 'Enter the port:', placeholder: '3306');
        $username = $this->askOrUseENV(argument: 'username', env: 'DB_USERNAME', question: 'Enter the username:', placeholder: 'paymenter');
        $password = password("Enter the password for user '$username':", required: true);
        $systemKey = password("Enter Blesta system_key (leave empty to skip password compatibility):", required: false);

        try {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->info('Connected to Blesta database, Starting migration...');

            try {
                DB::table('settings')
                    ->where('settingable_type', Server::class)
                    ->where('key', 'api_key')
                    ->delete();
            } catch (\Exception $e) {
            }

            if (!empty($systemKey)) {
                Setting::updateOrCreate(
                    ['key' => 'blesta_system_key'],
                    ['value' => $systemKey]
                );
            }

            $this->prepareDatabase();

            DB::statement('SET foreign_key_checks=0');

            DB::table('currencies')->truncate();
            $this->importCurrencies();
            $this->importAdmins();
            $this->importUsers();
            $this->importCategories();
            $this->importProducts();
            $this->importTickets();
            $this->importServices();
            $this->importCancellations();
            $this->importInvoices();
            $this->importInvoiceItems();
            $this->importPayments();
            $this->importCoupons();

            DB::statement('SET foreign_key_checks=1');

            SettingsProvider::flushCache();
        } catch (PDOException $e) {
            $this->fail('Connection failed: ' . $e->getMessage());
        }
    }

    private function prepareDatabase()
    {
        $appUrl = Setting::where('key', 'app_url')->value('value');
        $companyName = Setting::where('key', 'company_name')->value('value');
        $blestaSystemKey = Setting::where('key', 'blesta_system_key')->value('value');
        
        $this->call('migrate:fresh', ['--force' => true]);
        $this->call('db:seed', ['--force' => true]);
        $this->call('db:seed', ['--class' => 'CustomPropertySeeder', '--force' => true]);
        
        if ($appUrl) {
            Setting::updateOrCreate(
                ['key' => 'app_url'],
                ['value' => $appUrl]
            );
        }
        if ($companyName) {
            Setting::updateOrCreate(
                ['key' => 'company_name'],
                ['value' => $companyName]
            );
        }
        if ($blestaSystemKey) {
            Setting::updateOrCreate(
                ['key' => 'blesta_system_key'],
                ['value' => $blestaSystemKey]
            );
        }
    }

    protected function askOrUseENV(string $argument, string $env, string $question, string $placeholder): string
    {
        $arg_value = $this->argument($argument);
        if ($arg_value) {
            return $arg_value;
        }

        $env_value = env($env);
        if (!is_null($env_value) && $env_value !== '') {
            return $env_value;
        }

        return text($question, required: true, placeholder: $placeholder);
    }

    protected function migrateInBatch(string $table, string $query, Closure $processor)
    {
        /**
         * @var PDOStatement $stmt
         */
        $stmt = $this->pdo->prepare($query);

        $offset = 0;
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $this->batchSize, PDO::PARAM_INT);
        $stmt->execute();

        $nRows = $this->pdo->query('SELECT COUNT(*) FROM ' . $table)->fetchColumn();

        if ($nRows <= $this->batchSize) {
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            try {
                $processor($records);
            } catch (Throwable $th) {
                Log::error($th);
                $this->fail($th->getMessage());
            }
        } else {
            $bar = $this->output->createProgressBar(round($nRows / $this->batchSize));
            $bar->setFormat("Batch: %current%/%max% [%bar%] %percent:3s%%\n");
            $bar->start();
            while ($records = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                $offset += $this->batchSize;
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();

                try {
                    $processor($records);
                } catch (Throwable $th) {
                    Log::error($th);
                    $this->fail($th->getMessage());
                }

                $bar->advance();
            }

            $bar->finish();
        }

        $this->info('Done.');
    }

    private function count(string $table): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM ' . $table);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['count'] ?? 0);
    }

    private function importCurrencies()
    {
        $this->info('Importing currencies... (' . $this->count('currencies') . ' records)');

        $this->migrateInBatch('currencies', 'SELECT * FROM currencies LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                $format = '1,000.00';
                if (isset($record['format'])) {
                    $format = match ($record['format']) {
                        1 => '1,000.00',
                        2 => '1.000,00',
                        default => '1,000.00',
                    };
                }

                $data[] = [
                    'name' => $record['code'],
                    'code' => $record['code'],
                    'prefix' => $record['prefix'] ?? '',
                    'suffix' => $record['suffix'] ?? '',
                    'format' => $format,
                ];
                if (isset($record['default']) && $record['default'] == 1) {
                    Setting::updateOrCreate(
                        ['key' => 'default_currency'],
                        ['value' => $record['code']]
                    );
                }
            }

            DB::table('currencies')->insert($data);
        });
    }

    private function importUsers()
    {
        $this->info('Importing users... (' . $this->count('clients') . ' records)');
        $customProperties = CustomProperty::where('model', User::class)->get()->keyBy('key');

        $this->migrateInBatch('clients', 'SELECT * FROM clients LIMIT :limit OFFSET :offset', function ($records) use ($customProperties) {
            $data = [];
            $properties = [];
            $credits = [];

            foreach ($records as $record) {
                $currency = null;
                $currencyCode = 'USD';
                try {
                    if (isset($record['currency']) && $record['currency']) {
                        $currencyStmt = $this->pdo->prepare('SELECT * FROM currencies WHERE id = :id LIMIT 1');
                        $currencyStmt->bindValue(':id', $record['currency'], PDO::PARAM_INT);
                        $currencyStmt->execute();
                        $currency = $currencyStmt->fetch(PDO::FETCH_ASSOC);
                        if ($currency) {
                            $currencyCode = $currency['code'];
                        }
                    } else {
                        $currencyStmt = $this->pdo->prepare('SELECT * FROM currencies LIMIT 1');
                        $currencyStmt->execute();
                        $currency = $currencyStmt->fetch(PDO::FETCH_ASSOC);
                        if ($currency) {
                            $currencyCode = $currency['code'];
                        }
                    }
                } catch (PDOException $e) {
                }

                $contactStmt = $this->pdo->prepare('SELECT * FROM contacts WHERE client_id = :client_id AND contact_type = \'primary\' LIMIT 1');
                $contactStmt->bindValue(':client_id', $record['id'], PDO::PARAM_INT);
                $contactStmt->execute();
                $contact = $contactStmt->fetch(PDO::FETCH_ASSOC);

                if (!$contact) {
                    $contactStmt2 = $this->pdo->prepare('SELECT * FROM contacts WHERE client_id = :client_id LIMIT 1');
                    $contactStmt2->bindValue(':client_id', $record['id'], PDO::PARAM_INT);
                    $contactStmt2->execute();
                    $contact = $contactStmt2->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$contact) {
                        continue;
                    }
                }

                if (DB::table('users')->where('id', $record['id'])->exists()) {
                    continue;
                }

                $userStmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :user_id LIMIT 1');
                $userStmt->bindValue(':user_id', $record['user_id'], PDO::PARAM_INT);
                $userStmt->execute();
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);

                $data[] = [
                    'id' => $record['id'],
                    'first_name' => $contact['first_name'] ?? '',
                    'last_name' => $contact['last_name'] ?? '',
                    'email' => $contact['email'] ?? '',
                    'password' => $user['password'] ?? bcrypt(\Str::random(16)),
                    'email_verified_at' => $user['date_added'] ?? null,
                    'updated_at' => $record['date_updated'] ?? $record['date_added'] ?? $contact['date_added'] ?? now(),
                    'created_at' => $record['date_added'] ?? $contact['date_added'] ?? now(),
                ];

                foreach ($customProperties as $key => $property) {
                    $blestaKey = match ($key) {
                        'address' => 'address1',
                        'city' => 'city',
                        'state' => 'state',
                        'postcode' => 'zip',
                        'country' => 'country',
                        'phonenumber' => 'phone',
                        'company_name' => 'company',
                        default => $key,
                    };
                    
                    $value = null;
                    
                    if ($key === 'phonenumber') {
                        try {
                            $phoneStmt = $this->pdo->prepare('SELECT number FROM contact_numbers WHERE contact_id = :contact_id AND type = \'phone\' LIMIT 1');
                            $phoneStmt->bindValue(':contact_id', $contact['id'], PDO::PARAM_INT);
                            $phoneStmt->execute();
                            $phoneResult = $phoneStmt->fetch(PDO::FETCH_ASSOC);
                            if ($phoneResult && $phoneResult['number']) {
                                $value = $phoneResult['number'];
                            }
                        } catch (PDOException $e) {
                        }
                    } else {
                        if (isset($contact[$blestaKey]) && $contact[$blestaKey] !== '') {
                            $value = $contact[$blestaKey];
                        } elseif (isset($record[$blestaKey]) && $record[$blestaKey] !== '') {
                            $value = $record[$blestaKey];
                        }
                    }

                    if ($value !== null && $value !== '') {
                        array_push($properties, [
                            'key' => $key,
                            'value' => $value,
                            'model_id' => $record['id'],
                            'model_type' => User::class,
                            'name' => $property->name,
                            'custom_property_id' => $property->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                try {
                    $creditStmt = $this->pdo->prepare('SELECT SUM(amount) as total FROM client_credits WHERE client_id = :client_id');
                    $creditStmt->bindValue(':client_id', $record['id'], PDO::PARAM_INT);
                    $creditStmt->execute();
                    $creditResult = $creditStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($creditResult && $creditResult['total'] > 0) {
                        array_push($credits, [
                            'user_id' => $record['id'],
                            'amount' => $creditResult['total'],
                            'currency_code' => $currencyCode,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } catch (PDOException $e) {
                }
            }

            DB::table('users')->insert($data);
            if (count($properties) > 0) {
                DB::table('properties')->insert($properties);
            }
            if (count($credits) > 0) {
                DB::table('credits')->insert($credits);
            }
        });
    }

    private function importAdmins()
    {
        $this->info('Importing admins... (' . $this->count('staff') . ' records)');

        $adminRole = \App\Models\Role::firstOrCreate(
            ['name' => 'admin'],
            ['permissions' => ['*']]
        );
        $adminRoleId = $adminRole->id;

        $this->migrateInBatch('staff', 'SELECT * FROM staff LIMIT :limit OFFSET :offset', function ($records) use ($adminRoleId) {
            $data = [];
            foreach ($records as $record) {
                if (($record['status'] ?? 'active') !== 'active') {
                    continue;
                }

                if (DB::table('users')->where('email', $record['email'])->exists()) {
                    continue;
                }

                $isAdmin = false;
                try {
                    $groupStmt = $this->pdo->prepare('SELECT * FROM staff_groups WHERE id = :id LIMIT 1');
                    $groupStmt->bindValue(':id', $record['group_id'] ?? 0, PDO::PARAM_INT);
                    $groupStmt->execute();
                    $group = $groupStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($group) {
                        $groupName = strtolower($group['name'] ?? '');
                        if (strpos($groupName, 'admin') !== false || strpos($groupName, 'administrator') !== false) {
                            $isAdmin = true;
                        }
                    }
                } catch (PDOException $e) {
                    if (($record['group_id'] ?? 0) == 1) {
                        $isAdmin = true;
                    }
                }

                if (!$isAdmin && empty($data)) {
                    $isAdmin = true;
                }

                if (!$isAdmin) {
                    continue;
                }

                $userPassword = null;
                try {
                    if (!empty($record['user_id'])) {
                        $userStmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :user_id LIMIT 1');
                        $userStmt->bindValue(':user_id', $record['user_id'], PDO::PARAM_INT);
                        $userStmt->execute();
                        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                        if ($user && !empty($user['password'])) {
                            $userPassword = $user['password'];
                        }
                    }
                } catch (PDOException $e) {
                }

                $data[] = [
                    'id' => $record['id'],
                    'first_name' => $record['first_name'] ?? '',
                    'last_name' => $record['last_name'] ?? '',
                    'email' => $record['email'] ?? '',
                    'password' => $userPassword ?? bcrypt(\Str::random(16)),
                    'role_id' => $adminRoleId, // Admin role
                    'email_verified_at' => $record['date_created'] ?? null,
                    'created_at' => $record['date_created'] ?? now(),
                    'updated_at' => $record['date_updated'] ?? $record['date_created'] ?? now(),
                ];
            }

            if (count($data) > 0) {
                DB::table('users')->insert($data);
            }
        });
    }

    private function importCategories()
    {
        $this->info('Importing categories... (' . $this->count('package_groups') . ' records)');

        $this->migrateInBatch('package_groups', 'SELECT * FROM package_groups LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                $data[] = [
                    'id' => $record['id'],
                    'name' => $record['name'] ?? '',
                    'description' => $record['description'] ?? '',
                    'sort' => $record['order'] ?? 0,
                    'slug' => \Str::slug($record['name'] ?? 'category-' . $record['id']),
                    'created_at' => $record['date_created'] ?? now(),
                    'updated_at' => $record['date_updated'] ?? $record['date_created'] ?? now(),
                ];
            }

            DB::table('categories')->insert($data);
        });
    }


    private function importProducts()
    {
        $this->info('Importing products... (' . $this->count('packages') . ' records)');

        $this->migrateInBatch('packages', 'SELECT * FROM packages LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            $planData = [];
            $priceData = [];
            $groupProducts = []; // category_id => [ ['id' => productId, 'min_price' => float] ]

            foreach ($records as $record) {
                $categoryId = null;
                try {
                    $groupStmt = $this->pdo->prepare('SELECT package_group_id FROM package_group WHERE package_id = :package_id LIMIT 1');
                    $groupStmt->bindValue(':package_id', $record['id'], PDO::PARAM_INT);
                    $groupStmt->execute();
                    $groupResult = $groupStmt->fetch(PDO::FETCH_ASSOC);
                    if ($groupResult && $groupResult['package_group_id']) {
                        $categoryId = $groupResult['package_group_id'];
                    }
                } catch (PDOException $e) {
                    // Package group table doesn't exist, skip
                }

                $name = '';
                try {
                    $nameStmt = $this->pdo->prepare('SELECT name FROM package_names WHERE package_id = :package_id LIMIT 1');
                    $nameStmt->bindValue(':package_id', $record['id'], PDO::PARAM_INT);
                    $nameStmt->execute();
                    $nameResult = $nameStmt->fetch(PDO::FETCH_ASSOC);
                    if ($nameResult && $nameResult['name']) {
                        $name = $nameResult['name'];
                    }
                } catch (PDOException $e) {
                    // Package names table doesn't exist, use default
                }
                
                // Fallback to id if name is empty
                if (empty($name)) {
                    $name = 'Package ' . $record['id'];
                }

                $description = '';
                try {
                    $descStmt = $this->pdo->prepare('SELECT text, html FROM package_descriptions WHERE package_id = :package_id AND lang = :lang LIMIT 1');
                    $descStmt->bindValue(':package_id', $record['id'], PDO::PARAM_INT);
                    $descStmt->bindValue(':lang', 'en_us', PDO::PARAM_STR);
                    $descStmt->execute();
                    $descResult = $descStmt->fetch(PDO::FETCH_ASSOC);
                    if ($descResult && (!empty($descResult['html']) || !empty($descResult['text']))) {
                        $description = !empty($descResult['html']) ? $descResult['html'] : ($descResult['text'] ?? '');
                    }
                } catch (PDOException $e) {
                    try {
                        $descStmt = $this->pdo->prepare('SELECT text, html FROM package_descriptions WHERE package_id = :package_id LIMIT 1');
                        $descStmt->bindValue(':package_id', $record['id'], PDO::PARAM_INT);
                        $descStmt->execute();
                        $descResult = $descStmt->fetch(PDO::FETCH_ASSOC);
                        if ($descResult && (!empty($descResult['html']) || !empty($descResult['text']))) {
                            $description = !empty($descResult['html']) ? $descResult['html'] : ($descResult['text'] ?? '');
                        }
                    } catch (PDOException $e2) {
                    }
                }
                
                if (empty($description) || trim($description) === '') {
                    if (!empty($record['description_html']) && trim($record['description_html']) !== '') {
                        $description = $record['description_html'];
                    } elseif (!empty($record['description']) && trim($record['description']) !== '') {
                        $description = $record['description'];
                    }
                }

                $stock = null;
                $qty = isset($record['qty']) ? (int)$record['qty'] : null;
                if ($qty !== null && $qty > 0) {
                    $stock = $qty;
                }

                $allowQuantity = 'separated';

                $data[] = [
                    'id' => $record['id'],
                    'category_id' => $categoryId,
                    'name' => $name,
                    'description' => $description,
                    'slug' => \Str::slug($name ?: 'product-' . $record['id']),
                    'hidden' => ($record['status'] ?? 'active') !== 'active' ? 1 : 0,
                    'stock' => $stock,
                    'allow_quantity' => $allowQuantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                try {
                    $pricingStmt = $this->pdo->prepare('
                        SELECT pp.*, p.term, p.period, p.price, p.setup_fee, p.currency 
                        FROM package_pricing pp
                        INNER JOIN pricings p ON p.id = pp.pricing_id
                        WHERE pp.package_id = :package_id
                    ');
                    $pricingStmt->bindValue(':package_id', $record['id'], PDO::PARAM_INT);
                    $pricingStmt->execute();
                    $pricings = $pricingStmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    try {
                        $pricingStmt = $this->pdo->prepare('SELECT * FROM package_pricing WHERE package_id = :package_id');
                        $pricingStmt->bindValue(':package_id', $record['id'], PDO::PARAM_INT);
                        $pricingStmt->execute();
                        $pricings = $pricingStmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e2) {
                        $pricings = [];
                    }
                }

                if (empty($pricings)) {
                    $freePlanId = DB::table('plans')->insertGetId([
                        'priceable_id' => $record['id'],
                        'priceable_type' => Product::class,
                        'name' => 'Free',
                        'type' => 'free',
                        'billing_period' => 0,
                        'billing_unit' => null,
                    ]);
                    continue;
                }

                $minPrice = null;
                foreach ($pricings as $pricing) {
                    $currencyCode = 'USD';
                    if (isset($pricing['currency'])) {
                        if (strlen($pricing['currency']) == 3) {
                            $currencyCode = $pricing['currency'];
                        } else {
                            try {
                                $currencyStmt = $this->pdo->prepare('SELECT * FROM currencies WHERE id = :id LIMIT 1');
                                $currencyStmt->bindValue(':id', $pricing['currency'], PDO::PARAM_INT);
                                $currencyStmt->execute();
                                $currency = $currencyStmt->fetch(PDO::FETCH_ASSOC);
                                if ($currency) {
                                    $currencyCode = $currency['code'];
                                }
                            } catch (PDOException $e) {
                                // Currency lookup failed, use default
                            }
                        }
                    }

                    $period = $pricing['period'] ?? 'month';
                    $term = (int)($pricing['term'] ?? 1);
                    
                    $periodName = match (true) {
                        $period === 'onetime' => 'One-Time',
                        $period === 'year' && $term == 1 => 'Annually',
                        $period === 'year' && $term == 2 => 'Biennially',
                        $period === 'year' && $term == 3 => 'Triennially',
                        $period === 'month' && $term == 1 => 'Monthly',
                        $period === 'month' && $term == 3 => 'Quarterly',
                        $period === 'month' && $term == 6 => 'Semiannually',
                        $period === 'month' && $term == 12 => 'Annually',
                        $period === 'month' && $term == 24 => 'Biennially',
                        $period === 'month' && $term == 36 => 'Triennially',
                        $period === 'month' => 'Monthly', // Default to monthly for other month terms
                        $period === 'year' => 'Annually', // Default to annually for other year terms
                        $period === 'week' && $term == 1 => 'Monthly', // Map weekly to monthly as fallback
                        $period === 'day' && $term == 30 => 'Monthly', // Map 30 days to monthly
                        default => 'Monthly',
                    };

                    // Use term in planKey to ensure uniqueness for different terms with same period
                    $planKey = $record['id'] . '_' . $period . '_' . $term;

                    if ($period === 'onetime') {
                        $planData[$planKey] = [
                            'priceable_id' => $record['id'],
                            'priceable_type' => Product::class,
                            'name' => 'One-Time',
                            'type' => 'one-time',
                            'billing_period' => 0,
                            'billing_unit' => null,
                        ];

                        $priceData[$planKey][] = [
                            'currency_code' => $currencyCode,
                            'price' => (float)($pricing['price'] ?? 0),
                            'setup_fee' => (float)($pricing['setup_fee'] ?? 0),
                        ];
                        $minPrice = is_null($minPrice) ? (float)($pricing['price'] ?? 0) : min($minPrice, (float)($pricing['price'] ?? 0));
                    } else {
                        $planData[$planKey] = [
                            'priceable_id' => $record['id'],
                            'priceable_type' => Product::class,
                            'name' => $periodName,
                            'type' => 'recurring',
                            'billing_period' => match ($period) {
                                'month' => $term,
                                'year' => $term,
                                'week' => (int)ceil($term * 7 / 30), // Convert weeks to approximate months
                                'day' => (int)ceil($term / 30), // Convert days to approximate months
                                default => $term,
                            },
                            'billing_unit' => match ($period) {
                                'month', 'week', 'day' => 'month',
                                'year' => 'year',
                                default => 'month',
                            },
                        ];

                        $priceData[$planKey][] = [
                            'currency_code' => $currencyCode,
                            'price' => (float)($pricing['price'] ?? 0),
                            'setup_fee' => (float)($pricing['setup_fee'] ?? 0),
                        ];
                        $minPrice = is_null($minPrice) ? (float)($pricing['price'] ?? 0) : min($minPrice, (float)($pricing['price'] ?? 0));
                    }
                }

                // Track product for upgrades within the same category (package_group)
                if (!is_null($categoryId)) {
                    if (!isset($groupProducts[$categoryId])) {
                        $groupProducts[$categoryId] = [];
                    }
                    // Use 0 when price missing so it sorts lowest; still allows upgrade links to higher priced
                    $groupProducts[$categoryId][] = [
                        'id' => $record['id'],
                        'min_price' => $minPrice === null ? 0.0 : (float)$minPrice,
                    ];
                }
            }

            // Insert products first
            DB::table('products')->insert($data);

            // Insert plans and then prices
            foreach ($planData as $planKey => $plan) {
                $planId = DB::table('plans')->insertGetId($plan);

                if (isset($priceData[$planKey])) {
                    foreach ($priceData[$planKey] as &$price) {
                        $price['plan_id'] = $planId;
                    }
                    DB::table('prices')->insert($priceData[$planKey]);
                }
            }

            // Create upgrade links within each Blesta package group (category)
            // For each group, sort products by min_price ascending, and allow upgrades from lower to higher
            foreach ($groupProducts as $catId => $productsInGroup) {
                usort($productsInGroup, function ($a, $b) {
                    if ($a['min_price'] == $b['min_price']) {
                        return $a['id'] <=> $b['id'];
                    }
                    return $a['min_price'] <=> $b['min_price'];
                });

                $upgrades = [];
                $count = count($productsInGroup);
                for ($i = 0; $i < $count; $i++) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        $upgrades[] = [
                            'product_id' => $productsInGroup[$i]['id'],
                            'upgrade_id' => $productsInGroup[$j]['id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                if (!empty($upgrades)) {
                    DB::table('product_upgrades')->insert($upgrades);
                }
            }
        });
    }

    private function importTickets()
    {
        $this->info('Importing tickets... (' . $this->count('support_tickets') . ' records)');

        $this->migrateInBatch('support_tickets', 'SELECT * FROM support_tickets LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            $messages = [];
            
            foreach ($records as $record) {
                $userId = null;
                if (!empty($record['client_id'])) {
                    if (DB::table('users')->where('id', $record['client_id'])->exists()) {
                        $userId = $record['client_id'];
                    } else {
                        $contactStmt = $this->pdo->prepare('SELECT * FROM contacts WHERE client_id = :client_id AND contact_type = \'primary\' LIMIT 1');
                        $contactStmt->bindValue(':client_id', $record['client_id'], PDO::PARAM_INT);
                        $contactStmt->execute();
                        $contact = $contactStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($contact && !empty($contact['email'])) {
                            $user = DB::table('users')->where('email', $contact['email'])->first();
                            if ($user) {
                                $userId = $user->id;
                            }
                        }
                    }
                }
                
                if (!$userId && !empty($record['email'])) {
                    $user = DB::table('users')->where('email', $record['email'])->first();
                    if ($user) {
                        $userId = $user->id;
                    }
                }
                
                if (!$userId) {
                    continue;
                }

                $assignedTo = null;
                if (!empty($record['staff_id'])) {
                    $staffStmt = $this->pdo->prepare('SELECT * FROM staff WHERE id = :id LIMIT 1');
                    $staffStmt->bindValue(':id', $record['staff_id'], PDO::PARAM_INT);
                    $staffStmt->execute();
                    $staff = $staffStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($staff && !empty($staff['email'])) {
                        $staffUser = DB::table('users')->where('email', $staff['email'])->first();
                        if ($staffUser) {
                            $assignedTo = $staffUser->id;
                        }
                    }
                }

                // Get department
                $departmentName = null;
                if (!empty($record['department_id'])) {
                    try {
                        $departmentStmt = $this->pdo->prepare('SELECT * FROM support_departments WHERE id = :id LIMIT 1');
                        $departmentStmt->bindValue(':id', $record['department_id'], PDO::PARAM_INT);
                        $departmentStmt->execute();
                        $department = $departmentStmt->fetch(PDO::FETCH_ASSOC);
                        if ($department) {
                            $departmentName = $department['name'] ?? null;
                        }
                    } catch (PDOException $e) {
                        // Department table doesn't exist, skip
                    }
                }

                // Map Blesta status to Paymenter status
                $status = match ($record['status'] ?? 'open') {
                    'closed' => 'closed',
                    'trash' => 'closed',
                    'awaiting_reply' => 'replied',
                    'in_progress' => 'open',
                    'on_hold' => 'open',
                    'open' => 'open',
                    default => 'open',
                };

                // Map Blesta priority to Paymenter priority
                $priority = match ($record['priority'] ?? 'medium') {
                    'emergency', 'critical' => 'high',
                    'high' => 'high',
                    'medium' => 'medium',
                    'low' => 'low',
                    default => 'medium',
                };

                // Use summary as subject (Blesta uses 'summary', not 'subject')
                $subject = $record['summary'] ?? $record['subject'] ?? 'Ticket #' . $record['id'];

                $data[] = [
                    'id' => $record['id'],
                    'user_id' => $userId,
                    'assigned_to' => $assignedTo,
                    'subject' => $subject,
                    'status' => $status,
                    'priority' => $priority,
                    'department' => $departmentName,
                    'service_id' => !empty($record['service_id']) ? $record['service_id'] : null,
                    'created_at' => $record['date_added'] ?? now(),
                    'updated_at' => $record['date_updated'] ?? $record['date_added'] ?? now(),
                ];

                // Get ticket replies (support_replies table)
                // Blesta uses 'details' field for the message content, and 'type' can be 'reply', 'note', or 'log'
                try {
                    $replyStmt = $this->pdo->prepare('SELECT * FROM support_replies WHERE ticket_id = :ticket_id ORDER BY date_added ASC');
                    $replyStmt->bindValue(':ticket_id', $record['id'], PDO::PARAM_INT);
                    $replyStmt->execute();
                    $replyRecords = $replyStmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    $replyRecords = [];
                }

                // Create initial message from ticket summary (if no replies exist)
                if (empty($replyRecords)) {
                    $initialMessage = $record['summary'] ?? 'Ticket opened';
                    if (!empty($initialMessage)) {
                        $messages[] = [
                            'ticket_id' => $record['id'],
                            'user_id' => $userId,
                            'message' => $initialMessage,
                            'created_at' => $record['date_added'] ?? now(),
                            'updated_at' => $record['date_added'] ?? now(),
                        ];
                    }
                }

                // Process each reply
                foreach ($replyRecords as $reply) {
                    // Only import 'reply' type messages (skip 'note' and 'log' internal notes)
                    if (($reply['type'] ?? 'reply') !== 'reply') {
                        continue;
                    }

                    $replyUserId = null;
                    $messageText = $reply['details'] ?? ''; // Blesta uses 'details' not 'detail'
                    
                    // Skip empty messages
                    if (empty($messageText) || trim($messageText) === '') {
                        continue;
                    }

                    // Determine who sent the reply
                    if (!empty($reply['staff_id'])) {
                        // Staff reply - first check if staff was imported (staff.id = users.id in Paymenter)
                        // Since we import staff with their Blesta ID, we can match directly
                        $staffUser = DB::table('users')->where('id', $reply['staff_id'])->whereNotNull('role_id')->first();
                        
                        if ($staffUser) {
                            // Found staff user by ID (they were imported as admin)
                            $replyUserId = $staffUser->id;
                        } else {
                            // Fallback: try to find by email
                            $staffStmt = $this->pdo->prepare('SELECT * FROM staff WHERE id = :id LIMIT 1');
                            $staffStmt->bindValue(':id', $reply['staff_id'], PDO::PARAM_INT);
                            $staffStmt->execute();
                            $staff = $staffStmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($staff && !empty($staff['email'])) {
                                $staffUserByEmail = DB::table('users')->where('email', $staff['email'])->whereNotNull('role_id')->first();
                                if ($staffUserByEmail) {
                                    $replyUserId = $staffUserByEmail->id;
                                }
                            }
                        }
                    } elseif (!empty($reply['contact_id'])) {
                        // Client contact reply
                        $contactStmt = $this->pdo->prepare('SELECT * FROM contacts WHERE id = :id LIMIT 1');
                        $contactStmt->bindValue(':id', $reply['contact_id'], PDO::PARAM_INT);
                        $contactStmt->execute();
                        $contact = $contactStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($contact && !empty($contact['email'])) {
                            // Find user by email (should be a client, not admin)
                            $contactUser = DB::table('users')->where('email', $contact['email'])->whereNull('role_id')->first();
                            if ($contactUser) {
                                $replyUserId = $contactUser->id;
                            } else {
                                // Also check if they exist as admin (in case they're both client and staff)
                                $contactUserAdmin = DB::table('users')->where('email', $contact['email'])->first();
                                if ($contactUserAdmin) {
                                    $replyUserId = $contactUserAdmin->id;
                                }
                            }
                        }
                    }

                    // Fallback to ticket owner if we can't find the reply sender
                    if (!$replyUserId) {
                        $replyUserId = $userId;
                    }

                    $messages[] = [
                        'ticket_id' => $record['id'],
                        'user_id' => $replyUserId,
                        'message' => $messageText,
                        'created_at' => $reply['date_added'] ?? now(),
                        'updated_at' => $reply['date_added'] ?? now(),
                    ];
                }
            }

            if (count($data) > 0) {
                DB::table('tickets')->insert($data);
            }
            if (count($messages) > 0) {
                DB::table('ticket_messages')->insert($messages);
            }
        });
    }


    private function importServices()
    {
        $this->info('Importing services... (' . $this->count('services') . ' records)');

        $this->migrateInBatch('services', 'SELECT * FROM services LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                // Get package pricing to get package_id and pricing details
                // Try newer Blesta structure first (package_pricing -> pricings)
                $packageId = null;
                $pricing = null;
                $currencyCode = 'USD';
                
                try {
                    $pricingStmt = $this->pdo->prepare('
                        SELECT pp.package_id, p.term, p.period, p.price, p.setup_fee, p.currency 
                        FROM package_pricing pp
                        INNER JOIN pricings p ON p.id = pp.pricing_id
                        WHERE pp.id = :pricing_id LIMIT 1
                    ');
                    $pricingStmt->bindValue(':pricing_id', $record['pricing_id'], PDO::PARAM_INT);
                    $pricingStmt->execute();
                    $pricing = $pricingStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($pricing) {
                        $packageId = $pricing['package_id'];
                        // Currency is a code (char(3)) in newer Blesta
                        if (isset($pricing['currency']) && strlen($pricing['currency']) == 3) {
                            $currencyCode = $pricing['currency'];
                        }
                    }
                } catch (PDOException $e) {
                    // Try older Blesta structure (package_pricing has all fields)
                    try {
                        $pricingStmt = $this->pdo->prepare('SELECT * FROM package_pricing WHERE id = :id LIMIT 1');
                        $pricingStmt->bindValue(':id', $record['pricing_id'], PDO::PARAM_INT);
                        $pricingStmt->execute();
                        $pricing = $pricingStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($pricing) {
                            $packageId = $pricing['package_id'];
                            // Currency might be a code or ID
                            if (isset($pricing['currency'])) {
                                if (strlen($pricing['currency']) == 3) {
                                    $currencyCode = $pricing['currency'];
                                } else {
                                    // It's a currency ID, look it up
                                    $currencyStmt = $this->pdo->prepare('SELECT * FROM currencies WHERE id = :id LIMIT 1');
                                    $currencyStmt->bindValue(':id', $pricing['currency'], PDO::PARAM_INT);
                                    $currencyStmt->execute();
                                    $currency = $currencyStmt->fetch(PDO::FETCH_ASSOC);
                                    if ($currency) {
                                        $currencyCode = $currency['code'];
                                    }
                                }
                            }
                        }
                    } catch (PDOException $e2) {
                        // Pricing lookup failed, skip this service
                        continue;
                    }
                }

                if (!$packageId || !$pricing) {
                    continue;
                }

                $period = $pricing['period'] ?? 'month';
                $term = $pricing['term'] ?? 1;
                
                // Map period and term to Paymenter period names
                $periodName = match (true) {
                    $period === 'onetime' => 'One-Time',
                    $period === 'year' && $term == 1 => 'Annually',
                    $period === 'year' && $term == 2 => 'Biennially',
                    $period === 'year' && $term == 3 => 'Triennially',
                    $period === 'month' && $term == 1 => 'Monthly',
                    $period === 'month' && $term == 3 => 'Quarterly',
                    $period === 'month' && $term == 6 => 'Semiannually',
                    $period === 'month' && $term == 12 => 'Annually',
                    $period === 'month' && $term == 24 => 'Biennially',
                    $period === 'month' && $term == 36 => 'Triennially',
                    $period === 'month' => 'Monthly',
                    $period === 'year' => 'Annually',
                    default => 'Monthly',
                };

                // Find the plan - need to match by period and term
                $planId = DB::table('plans')
                    ->where('priceable_id', $packageId)
                    ->where('priceable_type', Product::class)
                    ->where('name', $periodName)
                    ->first()?->id;

                // If plan not found, try to find any plan for this product (fallback)
                if (!$planId) {
                    $fallbackPlan = DB::table('plans')
                        ->where('priceable_id', $packageId)
                        ->where('priceable_type', Product::class)
                        ->first();
                    $planId = $fallbackPlan?->id;
                }

                // If still no plan, create a default one (to prevent 500 errors)
                if (!$planId && $packageId) {
                    $planId = DB::table('plans')->insertGetId([
                        'priceable_id' => $packageId,
                        'priceable_type' => Product::class,
                        'name' => $periodName ?: 'Monthly',
                        'type' => ($period === 'onetime') ? 'one-time' : 'recurring',
                        'billing_period' => ($period === 'onetime') ? 0 : $term,
                        'billing_unit' => ($period === 'onetime') ? null : (($period === 'year') ? 'year' : 'month'),
                    ]);
                    
                    // Create a price for this plan
                    DB::table('prices')->insert([
                        'plan_id' => $planId,
                        'currency_code' => $currencyCode,
                        'price' => (float)($pricing['price'] ?? 0),
                        'setup_fee' => (float)($pricing['setup_fee'] ?? 0),
                    ]);
                }

                // Ensure we have valid product_id and plan_id to prevent 500 errors
                if (!$packageId || !$planId) {
                    continue; // Skip services without valid product or plan
                }

                $data[] = [
                    'id' => $record['id'],
                    'order_id' => null,
                    'product_id' => $packageId,
                    'status' => match ($record['status'] ?? 'pending') {
                        'active' => 'active',
                        'suspended' => 'suspended',
                        'canceled' => 'cancelled',
                        'canceled_pending' => 'cancelled',
                        'pending' => 'pending',
                        'in_review' => 'pending',
                        default => 'pending',
                    },
                    'price' => (float)($pricing['price'] ?? 0),
                    'quantity' => (int)($record['qty'] ?? 1),
                    'user_id' => $record['client_id'],
                    'plan_id' => $planId,
                    'currency_code' => $currencyCode,
                    'expires_at' => $record['date_renews'] ?? null,
                    'created_at' => $record['date_added'] ?? now(),
                    'updated_at' => $record['date_added'] ?? now(),
                ];
            }

            DB::table('services')->insert($data);
        });
    }

    private function importCancellations()
    {
        // Blesta doesn't have a separate service_cancellations table
        // Cancellation info is stored in the services table (date_canceled, cancellation_reason)
        $this->info('Creating cancellations from services table...');
            
            // Get canceled services from services table
            // Note: cancellation_reason might not exist in all Blesta versions
            try {
                $stmt = $this->pdo->prepare('SELECT id, date_canceled, cancellation_reason, date_renews FROM services WHERE (status = "canceled" OR date_canceled IS NOT NULL)');
                $stmt->execute();
                $serviceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // cancellation_reason field doesn't exist, try without it
                $stmt = $this->pdo->prepare('SELECT id, date_canceled, date_renews FROM services WHERE (status = "canceled" OR date_canceled IS NOT NULL)');
                $stmt->execute();
                $serviceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            $data = [];
            foreach ($serviceRecords as $record) {
                // Determine if cancellation is immediate or end of period
                $type = 'immediate';
                if (isset($record['date_canceled']) && $record['date_canceled'] && isset($record['date_renews']) && $record['date_renews']) {
                    // If cancel date is the same as renewal date, it's end of period
                    if ($record['date_canceled'] == $record['date_renews']) {
                        $type = 'end_of_period';
                    } elseif (strtotime($record['date_canceled']) > time()) {
                        // If cancel date is in the future, it's end of period
                        $type = 'end_of_period';
                    }
                }
                
                $data[] = [
                    'service_id' => $record['id'],
                    'reason' => mb_substr($record['cancellation_reason'] ?? '', 0, 255),
                    'type' => $type,
                    'created_at' => $record['date_canceled'] ?? now(),
                    'updated_at' => $record['date_canceled'] ?? now(),
                ];
            }

            if (count($data) > 0) {
                DB::table('service_cancellations')->insert($data);
                $this->info('Created ' . count($data) . ' cancellations from services.');
            } else {
                $this->info('No cancellations found.');
            }
    }

    private function importInvoices()
    {
        $this->info('Importing invoices... (' . $this->count('invoices') . ' records)');

        $this->migrateInBatch('invoices', 'SELECT * FROM invoices LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                // Check if client exists
                $clientStmt = $this->pdo->prepare('SELECT id FROM clients WHERE id = :id LIMIT 1');
                $clientStmt->bindValue(':id', $record['client_id'], PDO::PARAM_INT);
                $clientStmt->execute();
                $client = $clientStmt->fetch(PDO::FETCH_ASSOC);

                if (!$client) {
                    continue;
                }

                // Currency is already in the invoice table as a code (char(3))
                $currencyCode = $record['currency'] ?? 'USD';
                
                // Determine invoice number - Blesta uses id_format and id_value
                $invoiceNumber = $record['id'];
                if (!empty($record['id_format']) && !empty($record['id_value'])) {
                    // Replace placeholder in id_format with id_value
                    $invoiceNumber = str_replace('{num}', $record['id_value'], $record['id_format']);
                    if ($invoiceNumber == $record['id_format']) {
                        // No replacement happened, use id_value directly
                        $invoiceNumber = $record['id_value'];
                    }
                }

                // Determine status - Blesta uses 'active', 'draft', 'void'
                // Check if invoice is paid by looking at transaction_applied table
                $status = match ($record['status'] ?? 'active') {
                    'void' => 'cancelled',
                    'draft' => 'unpaid',
                    default => 'unpaid',
                };
                
                // Check if invoice is paid by checking transaction_applied
                try {
                    // Get total paid from transaction_applied (amount applied to this invoice)
                    $paidStmt = $this->pdo->prepare('
                        SELECT SUM(ta.amount) as total_paid 
                        FROM transaction_applied ta
                        INNER JOIN transactions t ON t.id = ta.transaction_id
                        WHERE ta.invoice_id = :invoice_id AND t.status = "approved"
                    ');
                    $paidStmt->bindValue(':invoice_id', $record['id'], PDO::PARAM_INT);
                    $paidStmt->execute();
                    $paidResult = $paidStmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Get invoice total from invoice_lines
                    $totalStmt = $this->pdo->prepare('SELECT SUM(amount) as total FROM invoice_lines WHERE invoice_id = :invoice_id');
                    $totalStmt->bindValue(':invoice_id', $record['id'], PDO::PARAM_INT);
                    $totalStmt->execute();
                    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
                    
                    $totalPaid = (float)($paidResult['total_paid'] ?? 0);
                    $totalAmount = (float)($totalResult['total'] ?? 0);
                    
                    // Invoice is paid if total paid >= total amount (with small tolerance for rounding)
                    if ($totalPaid > 0 && $totalAmount > 0 && $totalPaid >= ($totalAmount - 0.01)) {
                        $status = 'paid';
                    }
                } catch (PDOException $e) {
                    // Transaction/invoice_lines lookup failed, use default status
                }

                $data[] = [
                    'id' => $record['id'],
                    'number' => $invoiceNumber,
                    'user_id' => $record['client_id'],
                    'status' => $status,
                    'currency_code' => $currencyCode,
                    'due_at' => $record['date_due'] ?? null,
                    'created_at' => $record['date_billed'] ?? now(),
                    'updated_at' => $record['date_closed'] ?? $record['date_billed'] ?? now(),
                ];
            }

            DB::table('invoices')->insert($data);
        });

        // Set invoice number in settings to highest imported invoice number + 1
        $highestInvoiceNumber = DB::table('invoices')->max('number');
        if ($highestInvoiceNumber) {
            Setting::updateOrCreate(
                ['key' => 'invoice_number'],
                ['value' => $highestInvoiceNumber + 1]
            );
        }
    }

    private function importInvoiceItems()
    {
        $this->info('Importing invoice items... (' . $this->count('invoice_lines') . ' records)');

        $this->migrateInBatch('invoice_lines', 'SELECT * FROM invoice_lines LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                $data[] = [
                    'id' => $record['id'],
                    'invoice_id' => $record['invoice_id'],
                    'description' => mb_substr($record['description'] ?? '', 0, 255),
                    'price' => $record['amount'] ?? 0,
                    'quantity' => $record['qty'] ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'reference_type' => ($record['service_id'] ?? null) ? Service::class : null,
                    'reference_id' => $record['service_id'] ?? null,
                ];
            }

            DB::table('invoice_items')->insert($data);
        });
    }

    private function importPayments()
    {
        // Count transaction_applied records (one transaction can apply to multiple invoices)
        try {
            $countStmt = $this->pdo->prepare('
                SELECT COUNT(*) as count 
                FROM transaction_applied ta
                INNER JOIN transactions t ON t.id = ta.transaction_id
                WHERE t.status = "approved"
            ');
            $countStmt->execute();
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            $count = (int) ($countResult['count'] ?? 0);
        } catch (PDOException $e) {
            $count = 0;
        }
        
        $this->info('Importing payments... (' . $count . ' records)');

        // Join transactions with transaction_applied to get invoice_id
        // Use transaction_applied as the base table since one transaction can apply to multiple invoices
        $this->migrateInBatch('transaction_applied', '
            SELECT ta.*, t.amount as transaction_amount, t.currency, t.type, t.transaction_type_id, 
                   t.account_id, t.gateway_id, t.transaction_id, t.parent_transaction_id, 
                   t.status, t.date_added
            FROM transaction_applied ta
            INNER JOIN transactions t ON t.id = ta.transaction_id
            WHERE t.status = "approved"
            LIMIT :limit OFFSET :offset
        ', function ($records) {
            $data = [];
            foreach ($records as $record) {
                // Skip if no invoice_id
                if (empty($record['invoice_id'])) {
                    continue;
                }

                // Get gateway_id if available
                $gatewayId = null;
                if (isset($record['gateway_id'])) {
                    // Try to find matching gateway in Paymenter
                    try {
                        $gatewayStmt = $this->pdo->prepare('SELECT name FROM gateways WHERE id = :id LIMIT 1');
                        $gatewayStmt->bindValue(':id', $record['gateway_id'], PDO::PARAM_INT);
                        $gatewayStmt->execute();
                        $gateway = $gatewayStmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($gateway) {
                            $paymenterGateway = DB::table('extensions')
                                ->where('type', 'gateway')
                                ->where('name', $gateway['name'] ?? '')
                                ->first();
                            $gatewayId = $paymenterGateway->id ?? null;
                        }
                    } catch (PDOException $e) {
                        // Gateway lookup failed, skip
                    }
                }

                // Use the amount from transaction_applied (amount applied to this specific invoice)
                // not the total transaction amount
                $data[] = [
                    // Don't set 'id' - let it auto-increment to avoid conflicts
                    'invoice_id' => $record['invoice_id'],
                    'gateway_id' => $gatewayId,
                    'amount' => $record['amount'] ?? 0, // Amount from transaction_applied
                    'fee' => null, // Blesta doesn't store fee per invoice application
                    'transaction_id' => $record['transaction_id'] ?? '',
                    'created_at' => $record['date'] ?? $record['date_added'] ?? now(),
                    'updated_at' => $record['date'] ?? $record['date_added'] ?? now(),
                ];
            }

            if (count($data) > 0) {
                DB::table('invoice_transactions')->insert($data);
            }
        });
    }

    private function importCoupons()
    {
        // Count only active coupons
        try {
            $countStmt = $this->pdo->prepare('SELECT COUNT(*) FROM coupons WHERE status = \'active\'');
            $countStmt->execute();
            $couponCount = $countStmt->fetchColumn();
        } catch (PDOException $e) {
            $couponCount = 0;
        }

        $this->info('Importing coupons... (' . $couponCount . ' records)');

        $this->migrateInBatch('coupons', 'SELECT * FROM coupons WHERE status = \'active\' LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            $couponProducts = [];

            foreach ($records as $record) {
                // Get coupon amount from coupon_amounts table
                // Blesta supports multiple currencies, we'll use the first one found
                $amountStmt = $this->pdo->prepare('SELECT * FROM coupon_amounts WHERE coupon_id = :coupon_id LIMIT 1');
                $amountStmt->bindValue(':coupon_id', $record['id'], PDO::PARAM_INT);
                $amountStmt->execute();
                $amount = $amountStmt->fetch(PDO::FETCH_ASSOC);

                if (!$amount) {
                    // Skip coupons without amounts
                    continue;
                }

                // Map Blesta coupon type to Paymenter type
                // Blesta: 'amount' = fixed amount, 'percent' = percentage
                // Paymenter: 'fixed' = fixed amount, 'percentage' = percentage
                $type = ($amount['type'] ?? 'percent') === 'amount' ? 'fixed' : 'percentage';
                $value = (float)($amount['amount'] ?? 0);

                // Map Blesta recurring to Paymenter recurring
                // Blesta: 0 = first billing cycle only, 1 = all billing cycles
                // Paymenter: 0 = all cycles, 1 = first cycle only, 2 = first 2 cycles, etc.
                $recurring = null;
                if (isset($record['recurring'])) {
                    $recurring = $record['recurring'] == 1 ? 0 : 1; // Invert: 1->0, 0->1
                }

                // Map max_qty to max_uses (0 means unlimited in Blesta, null in Paymenter)
                $maxUses = null;
                if (isset($record['max_qty']) && $record['max_qty'] > 0) {
                    $maxUses = (int)$record['max_qty'];
                }

                $data[] = [
                    'id' => $record['id'],
                    'code' => $record['code'],
                    'type' => $type,
                    'value' => $value,
                    'applies_to' => 'all', // Default to all (Blesta doesn't have this concept directly)
                    'recurring' => $recurring,
                    'max_uses' => $maxUses,
                    'starts_at' => $record['start_date'] ?? null,
                    'expires_at' => $record['end_date'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Get coupon packages (products this coupon applies to)
                $packageStmt = $this->pdo->prepare('SELECT package_id FROM coupon_packages WHERE coupon_id = :coupon_id');
                $packageStmt->bindValue(':coupon_id', $record['id'], PDO::PARAM_INT);
                $packageStmt->execute();
                $packages = $packageStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($packages as $package) {
                    $couponProducts[] = [
                        'coupon_id' => $record['id'],
                        'product_id' => $package['package_id'],
                    ];
                }
            }

            if (count($data) > 0) {
                DB::table('coupons')->insert($data);
            }
            if (count($couponProducts) > 0) {
                DB::table('coupon_products')->insert($couponProducts);
            }
        });
    }

}

