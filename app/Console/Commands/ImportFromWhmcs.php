<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\ConfigOption;
use App\Models\CustomProperty;
use App\Models\Product;
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

class ImportFromWhmcs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-from-whmcs {dbname} {username?} {host?} {port?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The PDO connection to WHMCS database
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

        try {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->info('Connected to WHMCS database, Starting migration...');

            $this->prepareDatabase();

            DB::statement('SET foreign_key_checks=0');

            // Remove default currencies
            DB::table('currencies')->truncate();
            // Import currencies
            $this->importCurrencies();
            $this->importUsers();
            $this->importAdmins();
            $this->importCategories();
            $this->importConfigOptions();
            $this->importProducts();
            $this->importTickets();
            $this->importOrders();
            $this->importServices();
            $this->importCancellations();
            $this->importInvoices();
            $this->importInvoiceItems();
            $this->importPayments();

            DB::statement('SET foreign_key_checks=1');

            SettingsProvider::flushCache();
        } catch (PDOException $e) {
            $this->fail('Connection failed: ' . $e->getMessage());
        }
    }

    private function prepareDatabase()
    {
        // Rerun migrations
        $this->call('migrate:fresh', ['--force' => true]);
        // Seed default data
        $this->call('db:seed', ['--force' => true]);
        $this->call('db:seed', ['--class' => 'CustomPropertySeeder', '--force' => true]);
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
        $this->info('Importing currencies... (' . $this->count('tblcurrencies') . ' records)');

        $this->migrateInBatch('tblcurrencies', 'SELECT * FROM tblcurrencies LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                $format = match ($record['format']) {
                    1 => '1 000.00', // 1234.56
                    2 => '1,000.00', // 1,234.56
                    4 => '1 000,00', // 1,234
                    default => '1.000,00', // 1.234,56
                };
                $data[] = [
                    'name' => $record['code'],
                    'code' => $record['code'],
                    'prefix' => $record['prefix'],
                    'suffix' => $record['suffix'],
                    'format' => $format,
                ];
                if ($record['default'] == 1) {
                    // Set default currency
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
        $this->info('Importing users... (' . $this->count('tblclients') . ' records)');
        $customProperties = CustomProperty::where('model', User::class)->get()->keyBy('key');

        $this->migrateInBatch('tblclients', 'SELECT * FROM tblclients LIMIT :limit OFFSET :offset', function ($records) use ($customProperties) {
            $data = [];
            $properties = [];
            $credits = [];

            foreach ($records as $record) {
                // Get client (join tblusers_clients on tblusers.id = tblusers_clients.auth_user_id and tblusers_clients.owner = 1)
                $stmt = $this->pdo->prepare('SELECT * FROM tblusers WHERE id = (SELECT auth_user_id FROM tblusers_clients WHERE client_id = :client_id AND owner = 1 LIMIT 1)');
                $stmt->bindValue(':client_id', $record['id'], PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    continue;
                }

                $data[] = [
                    'id' => $record['id'],
                    'first_name' => $record['firstname'],
                    'last_name' => $record['lastname'],
                    'email' => $record['email'],
                    'password' => $user['password'],
                    'email_verified_at' => $user['email_verified_at'] ? $user['email_verified_at'] : null,
                    'updated_at' => $record['updated_at'] == '0000-00-00 00:00:00' ? now() : $record['updated_at'],
                    'created_at' => $record['created_at'] == '0000-00-00 00:00:00' ? now() : $record['created_at'],
                ];

                // Custom properties
                foreach ($customProperties as $key => $property) {
                    // address1 -> address, companyname -> company_name
                    $whmcsKey = match ($key) {
                        'address' => 'address1',
                        'company_name' => 'companyname',
                        'postcode' => 'zip',
                        'phonenumber' => 'phone',
                        default => $key,
                    };
                    if (isset($record[$key]) && $record[$key] !== '') {
                        array_push($properties, [
                            'key' => $key,
                            'value' => $record[$whmcsKey],
                            'model_id' => $record['id'],
                            'model_type' => User::class,
                            'name' => $property->name,
                            'custom_property_id' => $property->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Credits
                if ($record['credit'] > 0) {
                    $currency = $this->pdo->prepare('SELECT * FROM tblcurrencies WHERE id = :id LIMIT 1');
                    $currency->bindValue(':id', $record['currency'], PDO::PARAM_INT);
                    $currency->execute();
                    $currency = $currency->fetch(PDO::FETCH_ASSOC);
                    array_push($credits, [
                        'user_id' => $record['id'],
                        'amount' => $record['credit'],
                        'currency_code' => $currency['code'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
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
        $this->info('Importing admins... (' . $this->count('tbladmins') . ' records)');

        $this->migrateInBatch('tbladmins', 'SELECT * FROM tbladmins LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                if ($record['roleid'] != 1 || $record['disabled'] == 1 || DB::table('users')->where('email', $record['email'])->exists()) {
                    // Only import administrators
                    continue;
                }
                $data[] = [
                    'id' => $record['id'],
                    'first_name' => $record['firstname'],
                    'last_name' => $record['lastname'],
                    'email' => $record['email'],
                    'password' => $record['password'],
                    'role_id' => 1, // Admin role
                    'email_verified_at' => null,
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['lastlogin'] ?? now(),
                ];
            }
        });
    }

    private function importCategories()
    {
        $this->info('Importing categories... (' . $this->count('tblproductgroups') . ' records)');

        $this->migrateInBatch('tblproductgroups', 'SELECT * FROM tblproductgroups LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                $data[] = [
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'description' => $record['tagline'],
                    'sort' => $record['order'],
                    'slug' => $record['slug'],
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }

            DB::table('categories')->insert($data);
        });
    }

    private function importConfigOptions()
    {
        $this->info('Importing config options... (' . $this->count('tblproductconfigoptions') . ' records)');

        $this->migrateInBatch('tblproductconfigoptions', 'SELECT * FROM tblproductconfigoptions LIMIT :limit OFFSET :offset', function ($records) {
            $planData = [];
            $priceData = [];

            // First, insert parent config options and track their new IDs
            foreach ($records as $record) {
                if (strpos($record['optionname'], '|') !== false) {
                    $environmentVariable = explode('|', $record['optionname'])[0];
                    $name = explode('|', $record['optionname'])[1] ?? $record['optionname'];
                } else {
                    $environmentVariable = null;
                    $name = $record['optionname'];
                }

                $parentData = [
                    'name' => $name,
                    'env_variable' => $environmentVariable,
                    'type' => match ($record['optiontype']) {
                        1 => 'select',
                        2 => 'radio',
                        3 => 'checkbox',
                        4 => 'number',
                        default => 'select',
                    },
                    'sort' => $record['order'],
                    'hidden' => $record['hidden'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insert parent and get the new Laravel-generated ID
                $newParentId = DB::table('config_options')->insertGetId($parentData);

                // Get child options for this parent
                $stmt = $this->pdo->prepare('SELECT * FROM tblproductconfigoptionssub WHERE configid = :configid');
                $stmt->bindValue(':configid', $record['id'], PDO::PARAM_INT);
                $stmt->execute();
                $optionRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($optionRecords as $option) {
                    if (strpos($option['optionname'], '|') !== false) {
                        $environmentVariable = explode('|', $option['optionname'])[0];
                        $name = explode('|', $option['optionname'])[1] ?? $option['optionname'];
                    } else {
                        $environmentVariable = null;
                        $name = $option['optionname'];
                    }

                    $childData = [
                        'parent_id' => $newParentId, // Use the new Laravel-generated parent ID
                        'name' => $name,
                        'env_variable' => $environmentVariable,
                        'sort' => $option['sortorder'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Insert child and get the new Laravel-generated ID
                    $newChildId = DB::table('config_options')->insertGetId($childData);

                    // Get pricing for the option
                    $stmt2 = $this->pdo->prepare('SELECT * FROM tblpricing WHERE type = "configoptions" AND relid = :relid');
                    $stmt2->bindValue(':relid', $option['id'], PDO::PARAM_INT);
                    $stmt2->execute();
                    $prices = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                    $this->priceMagic($prices, $planData, $priceData, ['id' => $newChildId, 'paytype' => 'recurring'], ConfigOption::class);
                }
            }

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
        });
    }

    private function priceMagic(&$prices, &$planData, &$priceData, $record, $priceableType = Product::class)
    {
        foreach ($prices as $pricing) {
            if ($record['paytype'] === 'onetime') {
                // One-time payment product, create a one-time plan
                $setupFee = $pricing['msetupfee'] ?? 0;

                // Create a unique key to link plan and price
                $planKey = $record['id'] . '_onetime';

                $planData[$planKey] = [
                    'priceable_id' => $record['id'],
                    'priceable_type' => $priceableType,
                    'name' => 'One-Time',
                    'type' => 'one-time',
                    'billing_period' => 0,
                    'billing_unit' => null,
                ];

                $currency = $this->pdo->prepare('SELECT * FROM tblcurrencies WHERE id = :id LIMIT 1');
                $currency->bindValue(':id', $pricing['currency'], PDO::PARAM_INT);
                $currency->execute();
                $currency = $currency->fetch(PDO::FETCH_ASSOC);

                $priceData[$planKey][] = [
                    'currency_code' => $currency['code'],
                    'price' => $pricing['monthly'],
                    'setup_fee' => $setupFee,
                ];

                continue;
            }

            foreach (['monthly', 'quarterly', 'semiannually', 'annually', 'biennially', 'triennially'] as $period) {
                if ($pricing[$period] > 0) {
                    $setupFee = match ($period) {
                        'monthly' => $pricing['msetupfee'],
                        'quarterly' => $pricing['qsetupfee'],
                        'semiannually' => $pricing['ssetupfee'],
                        'annually' => $pricing['asetupfee'],
                        'biennially' => $pricing['bsetupfee'],
                        'triennially' => $pricing['tsetupfee'],
                        default => 0,
                    };

                    // Create a unique key to link plan and price
                    $planKey = $record['id'] . '_' . $period;

                    $planData[$planKey] = [
                        'priceable_id' => $record['id'],
                        'priceable_type' => $priceableType,
                        'name' => ucfirst($period),
                        'type' => 'recurring',
                        'billing_period' => match ($period) {
                            'monthly' => 1,
                            'quarterly' => 3,
                            'semiannually' => 6,
                            'annually' => 1,
                            'biennially' => 2,
                            'triennially' => 3,
                            default => 1,
                        },
                        'billing_unit' => match ($period) {
                            'monthly', 'quarterly', 'semiannually' => 'month',
                            'annually', 'biennially', 'triennially' => 'year',
                            default => 'month',
                        },
                    ];

                    $currency = $this->pdo->prepare('SELECT * FROM tblcurrencies WHERE id = :id LIMIT 1');
                    $currency->bindValue(':id', $pricing['currency'], PDO::PARAM_INT);
                    $currency->execute();
                    $currency = $currency->fetch(PDO::FETCH_ASSOC);

                    $priceData[$planKey][] = [
                        'currency_code' => $currency['code'],
                        'price' => $pricing[$period],
                        'setup_fee' => $setupFee,
                    ];
                }

            }
        }

    }

    private function importProducts()
    {
        $this->info('Importing products... (' . $this->count('tblproducts') . ' records)');

        $this->migrateInBatch('tblproducts', 'SELECT * FROM tblproducts LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            $planData = [];
            $priceData = [];
            $upgrades = [];

            foreach ($records as $record) {
                $data[] = [
                    'id' => $record['id'],
                    'category_id' => $record['gid'],
                    'name' => $record['name'],
                    'description' => $record['description'],
                    'slug' => !empty($record['slug']) ? $record['slug'] : \Str::slug($record['name']),
                    'hidden' => $record['hidden'],
                    'stock' => $record['stockcontrol'] ? $record['qty'] : null,
                    'allow_quantity' => match ($record['allowqty']) {
                        1 => 'separated',
                        3 => 'combined',
                        default => 'disabled',
                    },
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];

                // Upgrades
                $stmt = $this->pdo->prepare('SELECT * FROM tblproduct_upgrade_products WHERE product_id = :product_id');
                $stmt->bindValue(':product_id', $record['id'], PDO::PARAM_INT);
                $stmt->execute();
                $upgradeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($upgradeRecords as $upgrade) {
                    $upgrades[] = [
                        'product_id' => $upgrade['product_id'],
                        'upgrade_id' => $upgrade['upgrade_product_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Config options
                $stmt = $this->pdo->prepare('SELECT * FROM tblproductconfiglinks WHERE pid = :pid');
                $stmt->bindValue(':pid', $record['id'], PDO::PARAM_INT);
                $stmt->execute();
                $configOptionRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($configOptionRecords as $configOptionGroupId) {
                    // Get the config option group
                    $configOptionGroup = $this->pdo->prepare('SELECT * FROM tblproductconfiggroups WHERE id = :id LIMIT 1');
                    $configOptionGroup->bindValue(':id', $configOptionGroupId['gid'], PDO::PARAM_INT);
                    $configOptionGroup->execute();
                    $configOptionGroup = $configOptionGroup->fetch(PDO::FETCH_ASSOC);
                    if (!$configOptionGroup) {
                        continue;
                    }
                    // Get config options in the group
                    $configOptions = $this->pdo->prepare('SELECT * FROM tblproductconfigoptions WHERE gid = :gid');
                    $configOptions->bindValue(':gid', $configOptionGroup['id'], PDO::PARAM_INT);
                    $configOptions->execute();
                    $configOptions = $configOptions->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($configOptions as $configOption) {
                        // Link config option to product
                        DB::table('config_option_products')->insert([
                            'product_id' => $record['id'],
                            'config_option_id' => $configOption['id'],
                        ]);
                    }
                }
            }

            // Insert products first
            DB::table('products')->insert($data);

            // Insert upgrades
            if (count($upgrades) > 0) {
                DB::table('product_upgrades')->insert($upgrades);
            }

            // Now process plans for all products in this batch
            foreach ($records as $record) {
                if ($record['paytype'] === 'free') {
                    // Free product, create a free plan
                    $planData[$record['id'] . '_free'] = [
                        'priceable_id' => $record['id'],
                        'priceable_type' => Product::class,
                        'name' => 'Free',
                        'type' => 'free',
                    ];

                    continue;
                }
                $stmt = $this->pdo->prepare('SELECT * FROM tblpricing WHERE type = "product" AND relid = :relid');
                $stmt->bindValue(':relid', $record['id'], PDO::PARAM_INT);
                $stmt->execute();
                $prices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $this->priceMagic($prices, $planData, $priceData, $record);
            }

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
        });
    }

    private function getUserIdTicket($message, &$userId)
    {
        if (!$userId) {
            if ($message['admin'] !== '') {
                // Admin is a first name + last name in WHMCS
                $user = DB::table('users')
                    ->where(DB::raw("CONCAT(first_name, ' ', last_name)"), $message['admin'])
                    ->orWhere('first_name', $message['admin'])
                    ->first();
                if ($user) {
                    $userId = $user->id;
                }
                // If not, we will make a admin account where we will attach all admin messages
                if (!$userId) {
                    $adminUserId = DB::table('users')->insertGetId([
                        'first_name' => $message['admin'],
                        'last_name' => '',
                        'email' => 'admin+' . strtolower(str_replace(' ', '_', $message['admin'])) . '@paymenter.org',
                        'password' => bcrypt(\Str::random(16)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $userId = $adminUserId;
                }
            }
            if (!$userId && DB::table('users')->where('email', $message['email'])->exists()) {
                $userRecord = DB::table('users')->where('email', $message['email'])->first();
                $userId = $userRecord->id;
            }
            if (!$userId) {
                // Create a user with the email
                $newUserId = DB::table('users')->insertGetId([
                    'first_name' => $message['name'],
                    'last_name' => '',
                    'email' => $message['email'],
                    'password' => bcrypt(\Str::random(16)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $userId = $newUserId;
            }
        }

        return $userId;
    }

    private function importTickets()
    {

        $this->info('Importing tickets... (' . $this->count('tbltickets') . ' records)');

        $this->migrateInBatch('tbltickets', 'SELECT * FROM tbltickets LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            $messages = [];
            foreach ($records as $record) {
                // Get user
                $user = $this->getUserIdTicket($record, $record['userid']);

                if (!$user) {
                    continue;
                }

                $departmentStmt = $this->pdo->prepare('SELECT * FROM tblticketdepartments WHERE id = :id LIMIT 1');
                $departmentStmt->bindValue(':id', $record['did'], PDO::PARAM_INT);
                $departmentStmt->execute();
                $department = $departmentStmt->fetch(PDO::FETCH_ASSOC);
                if ($department) {
                    // You can map department to category if needed
                    $departmentName = $department['name'];
                }

                $data[] = [
                    'id' => $record['id'],
                    'user_id' => $user,
                    'subject' => $record['title'],
                    'status' => match ($record['status']) {
                        'Answered' => 'replied',
                        'Closed' => 'closed',
                        default => 'open',
                    },
                    'priority' => match ($record['urgency']) {
                        'Low' => 'low',
                        'Medium' => 'medium',
                        'High' => 'high',
                        default => 'medium',
                    },
                    'department' => $departmentName ?? null,
                    'created_at' => $record['date'],
                    'updated_at' => $record['lastreply'],
                ];

                // Get ticket messages
                $stmt = $this->pdo->prepare('SELECT * FROM tblticketreplies WHERE tid = :tid ORDER BY date ASC');
                $stmt->bindValue(':tid', $record['id'], PDO::PARAM_INT);
                $stmt->execute();
                $messageRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($messageRecords as $message) {
                    // Get admin user if any
                    $userId = $this->getUserIdTicket($message, $message['userid']);
                    $messages[] = [
                        'ticket_id' => $record['id'],
                        'user_id' => $userId,
                        'message' => $message['message'],
                        'created_at' => $message['date'],
                        'updated_at' => $message['date'],
                    ];
                }
            }

            DB::table('tickets')->insert($data);
            if (count($messages) > 0) {
                DB::table('ticket_messages')->insert($messages);
            }
        });
    }

    private function importOrders()
    {
        $this->info('Importing orders... (' . $this->count('tblorders') . ' records)');

        $this->migrateInBatch('tblorders', 'SELECT * FROM tblorders LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                // Get currency from user
                $user = $this->pdo->prepare('SELECT * FROM tblcurrencies WHERE id = (SELECT currency FROM tblclients WHERE id = :client_id LIMIT 1) LIMIT 1');
                $user->bindValue(':client_id', $record['userid'], PDO::PARAM_INT);
                $user->execute();
                $user = $user->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    continue;
                }

                $data[] = [
                    'id' => $record['id'],
                    'user_id' => $record['userid'],
                    'currency_code' => $user['code'],
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ];
            }

            DB::table('orders')->insert($data);
        });
    }

    private function importServices()
    {
        $this->info('Importing services... (' . $this->count('tblhosting') . ' records)');

        $this->migrateInBatch('tblhosting', 'SELECT * FROM tblhosting LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                // Get currency from user
                $user = $this->pdo->prepare('SELECT * FROM tblcurrencies WHERE id = (SELECT currency FROM tblclients WHERE id = :client_id LIMIT 1) LIMIT 1');
                $user->bindValue(':client_id', $record['userid'], PDO::PARAM_INT);
                $user->execute();
                $user = $user->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    continue;
                }

                $planId = DB::table('plans')
                    ->where('priceable_id', $record['packageid'])
                    ->where('priceable_type', Product::class)
                    ->where('name', match ($record['billingcycle']) {
                        'Monthly' => 'Monthly',
                        'Quarterly' => 'Quarterly',
                        'Semi-Annually' => 'Semiannually',
                        'Annually' => 'Annually',
                        'Biennially' => 'Biennially',
                        'Triennially' => 'Triennially',
                        'One Time' => 'One-Time',
                        'Free Account' => 'Free',
                        default => 'Monthly',
                    })
                    ->first()?->id;

                $data[] = [
                    'id' => $record['id'],
                    'order_id' => $record['orderid'],
                    'product_id' => $record['packageid'],
                    'status' => match ($record['domainstatus']) {
                        'Active' => 'active',
                        'Suspended' => 'suspended',
                        'Terminated' => 'cancelled',
                        'Cancelled' => 'cancelled',
                        'Fraud' => 'cancelled',
                        'Pending' => 'pending',
                        default => 'pending',
                    },
                    'price' => $record['amount'],
                    'quantity' => $record['qty'],
                    'user_id' => $record['userid'],
                    'plan_id' => $planId,
                    'currency_code' => $user['code'],
                    'expires_at' => $record['nextduedate'] != '0000-00-00' ? $record['nextduedate'] : null,
                    'created_at' => $record['regdate'],
                    'updated_at' => $record['regdate'],
                ];
            }

            DB::table('services')->insert($data);
        });
    }

    private function importCancellations()
    {
        $this->info('Importing cancellations... (' . $this->count('tblcancelrequests') . ' records)');

        $this->migrateInBatch('tblcancelrequests', 'SELECT * FROM tblcancelrequests LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                $data[] = [
                    'id' => $record['id'],
                    'service_id' => $record['relid'],
                    'reason' => mb_substr($record['reason'], 0, 255),
                    'type' => $record['type'] == 'End of Billing Period' ? 'end_of_period' : 'immediate',
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ];
            }

            DB::table('service_cancellations')->insert($data);
        });
    }

    private function importInvoices()
    {
        $this->info('Importing invoices... (' . $this->count('tblinvoices') . ' records)');

        $this->migrateInBatch('tblinvoices', 'SELECT * FROM tblinvoices LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                // Get currency from user
                $user = $this->pdo->prepare('SELECT * FROM tblcurrencies WHERE id = (SELECT currency FROM tblclients WHERE id = :client_id LIMIT 1) LIMIT 1');
                $user->bindValue(':client_id', $record['userid'], PDO::PARAM_INT);
                $user->execute();
                $user = $user->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    continue;
                }

                $data[] = [
                    'id' => $record['id'],
                    'number' => !empty($record['invoicenum']) ? $record['invoicenum'] : null,
                    'user_id' => $record['userid'],
                    'status' => match ($record['status']) {
                        'Paid' => 'paid',
                        'Unpaid' => 'unpaid',
                        'Cancelled' => 'cancelled',
                        'Refunded' => 'cancelled',
                        default => 'unpaid',
                    },
                    'currency_code' => $user['code'],
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ];
            }

            DB::table('invoices')->insert($data);
        });

        // Set invoice number in settings to highest imported invoice number + 1
        $highestInvoiceNumber = DB::table('invoices')->max('number');
        Setting::updateOrCreate(
            ['key' => 'invoice_number'],
            ['value' => $highestInvoiceNumber + 1]
        );
    }

    private function importInvoiceItems()
    {
        $this->info('Importing invoice items... (' . $this->count('tblinvoiceitems') . ' records)');

        $this->migrateInBatch('tblinvoiceitems', 'SELECT * FROM tblinvoiceitems LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                $data[] = [
                    'id' => $record['id'],
                    'invoice_id' => $record['invoiceid'],
                    'description' => mb_substr($record['description'], 0, 255),
                    'price' => $record['amount'],
                    'created_at' => now(),
                    'updated_at' => now(),
                    'reference_type' => match ($record['type']) {
                        'Hosting' => Service::class,
                        'Domain' => null,
                        'Addon' => null,
                        'Upgrade' => null,
                        default => null,
                    },
                    'reference_id' => $record['relid'],
                ];
            }

            DB::table('invoice_items')->insert($data);
        });
    }

    private function importPayments()
    {
        $this->info('Importing payments... (' . $this->count('tblaccounts') . ' records)');

        $this->migrateInBatch('tblaccounts', 'SELECT * FROM tblaccounts LIMIT :limit OFFSET :offset', function ($records) {
            $data = [];
            foreach ($records as $record) {
                // Get currency from user
                $user = $this->pdo->prepare('SELECT * FROM tblcurrencies WHERE id = (SELECT currency FROM tblclients WHERE id = :client_id LIMIT 1) LIMIT 1');
                $user->bindValue(':client_id', $record['userid'], PDO::PARAM_INT);
                $user->execute();
                $user = $user->fetch(PDO::FETCH_ASSOC);
                if (!$user) {
                    continue;
                }

                $data[] = [
                    'id' => $record['id'],
                    'invoice_id' => $record['invoiceid'],
                    'amount' => $record['amountin'],
                    'transaction_id' => $record['transid'],
                    'created_at' => $record['date'],
                    'updated_at' => $record['date'],
                ];
            }

            DB::table('invoice_transactions')->insert($data);
        });
    }
}
