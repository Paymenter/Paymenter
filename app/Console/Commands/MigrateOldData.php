<?php

namespace App\Console\Commands;

use App\Classes\Settings;
use App\Helpers\ExtensionHelper;
use App\Models\ConfigOption;
use App\Models\Currency;
use App\Models\CustomProperty;
use App\Models\Gateway;
use App\Models\Price;
use App\Models\Server;
use App\Providers\SettingsProvider;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MigrateOldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-0.x {dbname} {username?} {host?} {port?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates all the data from paymenter version 0.x to 1.x';

    /**
     * The PDO connection to old database
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $currency_code;

    /**
     * @var int
     */
    protected $batchSize = 500;

    public function handle()
    {
        $dbname = $this->argument('dbname');
        $host = $this->askOrUseENV(argument: 'host', env: 'DB_HOST', question: 'Enter the host:', placeholder: 'localhost');
        $port = $this->askOrUseENV(argument: 'port', env: 'DB_PORT', question: 'Enter the port:', placeholder: '3306');
        $username = $this->askOrUseENV(argument: 'username', env: 'DB_USERNAME', question: 'Enter the username:', placeholder: 'paymenter');
        $password = password("Enter the password for user '$username':", required: true);

        try {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->info('Connected to old database, Starting migration...');

            DB::statement('SET foreign_key_checks=0');

            $this->defaultCurrency();
            $this->migrateSettings();
            $this->migrateConfigOptions();
            $this->migrateCoupons();
            $this->migrateCategories();
            $this->migrateUsers();
            $this->migrateTickets();
            $this->migrateTicketMessages();
            $this->migrateTaxRates();
            $this->migrateExtensions();
            $this->migrateProducts();
            $this->migrateProductUpgrades();
            $this->migrateConfigOptionProducts();
            $this->migratePlans();
            $this->migrateOrdersAndServices();
            $this->migrateServiceConfigs();
            $this->migrateServiceCancellations();
            $this->migrateInvoices();

            DB::statement('SET foreign_key_checks=1');

            SettingsProvider::flushCache();
        } catch (PDOException $e) {
            $this->fail('Connection failed: ' . $e->getMessage());
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

    protected function defaultCurrency()
    {
        // Get default currency
        $currency_settings = $this->pdo->query("SELECT * FROM `settings` WHERE `key` = 'currency' or `key` = 'currency_sign' or `key` = 'currency_position'")->fetchAll();
        $currency_settings = array_combine(array_column($currency_settings, 'key'), $currency_settings);
        $this->currency_code = $currency_settings['currency']['value'];

        // Remove all the pre-existing currencies, in case the user still want's to use single currency
        Currency::truncate();
        Currency::create([
            'name' => $this->currency_code,
            'code' => $this->currency_code,
            'prefix' => $currency_settings['currency_position']['value'] === 'left' ? $currency_settings['currency_sign']['value'] : null,
            'suffix' => $currency_settings['currency_position']['value'] === 'right' ? $currency_settings['currency_sign']['value'] : null,
            'format' => '1,000.00',
        ]);
    }

    protected function migrateSettings()
    {
        $stmt = $this->pdo->query('SELECT * FROM settings');
        $records = $stmt->fetchAll();

        // Map of settings which are just renamed
        $old_to_new_map = [
            // General
            'timezone' => 'timezone',
            'language' => 'app_language',
            'app_logo' => 'logo',
            'home_page_text' => 'theme_default_home_page_text',

            // Security
            'recaptcha_site_key' => 'captcha_site_key',
            'recaptcha_secret_key' => 'captcha_secret',

            // Social Login
            'google_enabled' => 'oauth_google',
            'google_client_id' => 'oauth_google_client_id',
            'google_client_secret' => 'oauth_google_client_secret',
            'github_enabled' => 'oauth_github',
            'github_client_id' => 'oauth_github_client_id',
            'github_client_secret' => 'oauth_github_client_secret',
            'discord_enabled' => 'oauth_discord',
            'discord_client_id' => 'oauth_discord_client_id',
            'discord_client_secret' => 'oauth_discord_client_secret',

            // Company Details
            'company_name' => 'company_name',
            'company_email' => 'company_email',
            'company_phone' => 'company_phone',
            'company_address' => 'company_address',
            'company_city' => 'company_city',
            'company_zip' => 'company_zip',

            // Tax
            'tax_enabled' => 'tax_enabled',
            'tax_type' => 'tax_type',

            // Mail
            'mail_disabled' => 'mail_disable',
            'must_verify_email' => 'mail_must_verify',
            'mail_host' => 'mail_host',
            'mail_port' => 'mail_port',
            'mail_username' => 'mail_username',
            'mail_password' => 'mail_password',
            'mail_encryption' => 'mail_encryption',
            'mail_from_address' => 'mail_from_address',
            'mail_from_name' => 'mail_from_name',

            // Other
            'currency' => 'default_currency',
        ];

        $settings = [];
        foreach ($records as $old_setting) {
            $key = $old_to_new_map[$old_setting['key']] ?? $old_setting['key'];
            $value = $old_setting['value'];

            // Migrate old settings directly if it is only renamed
            if (array_key_exists($old_setting['key'], $old_to_new_map)) {
                $avSetting = Settings::getSetting($key);

                $settings[] = [
                    'key' => $key,
                    'value' => $value,
                    'type' => $avSetting->database_type ?? 'string',
                    'settingable_type' => null,
                    'settingable_id' => null,
                    'encrypted' => $avSetting->encrypted ?? false,
                    'created_at' => $old_setting['created_at'],
                    'updated_at' => $old_setting['updated_at'],
                ];
            } else {
                // Manually migrate completely or partially changed settings
                if ($key === 'recaptcha_type') {
                    $setting_id = array_search('recaptcha', array_column($records, 'key'));
                    $captcha_disabled = $records[$setting_id]['value'] === '0';

                    $settings[] = [
                        'key' => 'captcha',
                        'value' => $captcha_disabled ? 'disabled' : match ($value) {
                            'v2' => 'recaptcha-v2',
                            'v3' => 'recaptcha-v3',
                            default => $value
                        },
                        'type' => 'string',
                        'settingable_type' => null,
                        'settingable_id' => null,
                        'encrypted' => false,
                        'created_at' => $old_setting['created_at'],
                        'updated_at' => $old_setting['updated_at'],
                    ];
                } elseif ($key === 'company_country') {
                    $settings[] = [
                        'key' => $key,
                        'value' => array_flip((array) config('app.countries'))[$value],
                        'type' => 'string',
                        'settingable_type' => null,
                        'settingable_id' => null,
                        'encrypted' => false,
                        'created_at' => $old_setting['created_at'],
                        'updated_at' => $old_setting['updated_at'],
                    ];
                } elseif (in_array($key, [
                    'requiredClientDetails_address',
                    'requiredClientDetails_city',
                    'requiredClientDetails_zip',
                    'requiredClientDetails_country',
                    'requiredClientDetails_phone',
                ])) {
                    $key = str_replace('requiredClientDetails_', '', $key);
                    $property = CustomProperty::where('name', $key)->first();
                    if ($property) {
                        $property->update(['required' => $value === '1']);
                    }
                }
            }
        }

        foreach ($settings as $value) {
            DB::table('settings')->updateOrInsert(['key' => $value['key']], $value);
        }
        $this->info('Migrated settings!');
    }

    protected function migrateConfigOptions()
    {
        $this->info('Migrating Config Options...');
        $this->migrateInBatch('configurable_options', 'SELECT c.id, c.name, c.type, c.order, c.hidden, c.group_id, g.name AS group_name, g.description AS group_description, g.products, c.created_at, c.updated_at FROM configurable_options as c JOIN configurable_option_groups as g ON c.group_id = g.id LIMIT :limit OFFSET :offset', function ($records) {
            $records = array_map(function ($record) {
                $option = explode('|', $record['name'], 2);
                $env_variable = $option[0];
                $name = $option[1] ?? $env_variable;

                return [
                    'id' => $record['id'],
                    'name' => trim($name),
                    'env_variable' => $env_variable ? trim($env_variable) : trim($name),
                    'type' => match ($record['type']) {
                        'quantity' => 'number',
                        'slider' => 'select',
                        default => $record['type']
                    },
                    // TODO: migrate sort, or not
                    'sort' => null,
                    'hidden' => $record['hidden'],
                    'parent_id' => null,

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('config_options')->insert($records);
        });

        $this->migrateInBatch('configurable_option_inputs', 'SELECT * FROM configurable_option_inputs LIMIT :limit OFFSET :offset', function ($option_inputs) {
            $option_inputs = array_map(function ($record) {
                $option = explode('|', $record['name'], 2);
                $env_variable = $option[0];
                $name = $option[1] ?? $env_variable;

                return [
                    'id' => $record['id'],
                    'name' => trim($name),
                    'env_variable' => $env_variable ? trim($env_variable) : trim($name),
                    'type' => null,
                    // TODO: migrate sort, or not
                    'sort' => null,
                    'hidden' => $record['hidden'],

                    'parent_id' => $record['option_id'],
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $option_inputs);

            foreach ($option_inputs as $input) {

                $input_id = $input['id'];
                $inputs_stmt = $this->pdo->query("SELECT * FROM configurable_option_input_pricing WHERE `input_id` = $input_id");
                $record = $inputs_stmt->fetchAll()[0];

                if (
                    is_null($record['monthly']) &&
                    is_null($record['quarterly']) &&
                    is_null($record['semi_annually']) &&
                    is_null($record['annually']) &&
                    is_null($record['biennially']) &&
                    is_null($record['triennially'])
                ) {
                    unset($input['id']);
                    $new_id = DB::table('config_options')->insertGetId($input);

                    // Option is free
                    $input_plan = [
                        'name' => 'Free',
                        'type' => 'free',
                        'priceable_id' => $new_id,
                        'priceable_type' => 'App\Models\ConfigOption',
                    ];

                    $plan_id = DB::table('plans')->insertGetId($input_plan);

                    continue;
                }

                if (
                    $record['monthly'] &&
                    is_null($record['quarterly']) &&
                    is_null($record['semi_annually']) &&
                    is_null($record['annually']) &&
                    is_null($record['biennially']) &&
                    is_null($record['triennially'])
                ) {
                    unset($input['id']);
                    $new_id = DB::table('config_options')->insertGetId($input);

                    // Option is one-time
                    $input_plan = [
                        'name' => 'One Time',
                        'type' => 'one-time',
                        'priceable_id' => $new_id,
                        'priceable_type' => 'App\Models\ConfigOption',
                    ];

                    $plan_id = DB::table('plans')->insertGetId($input_plan);

                    DB::table('prices')->insert([
                        'plan_id' => $plan_id,
                        'price' => $record['monthly'],
                        'setup_fee' => $record['monthly_setup'],
                        'currency_code' => $this->currency_code,
                    ]);

                    continue;
                }

                if (
                    $record['monthly'] &&
                    is_null($record['quarterly']) &&
                    is_null($record['semi_annually']) &&
                    is_null($record['annually']) &&
                    is_null($record['biennially']) &&
                    is_null($record['triennially'])
                ) {
                    unset($input['id']);
                    $new_id = DB::table('config_options')->insertGetId($input);

                    // Option is one-time
                    $input_plan = [
                        'name' => 'One Time',
                        'type' => 'one-time',
                        'priceable_id' => $new_id,
                        'priceable_type' => 'App\Models\ConfigOption',
                    ];

                    $plan_id = DB::table('plans')->insertGetId($input_plan);

                    DB::table('prices')->insert([
                        'plan_id' => $plan_id,
                        'price' => $record['monthly'],
                        'setup_fee' => $record['monthly_setup'],
                        'currency_code' => $this->currency_code,
                    ]);

                    continue;
                }

                if (
                    $record['monthly'] &&
                    ($record['quarterly'] ||
                        $record['semi_annually'] ||
                        $record['annually'] ||
                        $record['biennially'] ||
                        $record['triennially']
                    )
                ) {
                    unset($input['id']);
                    $new_id = DB::table('config_options')->insertGetId($input);

                    $common_fields = [
                        'type' => 'recurring',
                        'priceable_id' => $new_id,
                        'priceable_type' => 'App\Models\ConfigOption',
                    ];

                    $plans = [];

                    if ($record['monthly']) {
                        array_push($plans, array_merge([
                            'name' => 'Monthly',
                            'billing_period' => 1,
                            'billing_unit' => 'month',
                            'price' => [
                                'price' => $record['monthly'],
                                'setup_fee' => $record['monthly_setup'],
                                'currency_code' => $this->currency_code,
                            ],
                        ], $common_fields));
                    }

                    if ($record['quarterly']) {
                        array_push($plans, array_merge([
                            'name' => 'Quarterly',
                            'billing_period' => 3,
                            'billing_unit' => 'month',
                            'price' => [
                                'price' => $record['quarterly'],
                                'setup_fee' => $record['quarterly_setup'],
                                'currency_code' => $this->currency_code,
                            ],
                        ], $common_fields));
                    }

                    if ($record['semi_annually']) {
                        array_push($plans, array_merge([
                            'name' => 'Semi-Annually',
                            'billing_period' => 6,
                            'billing_unit' => 'month',
                            'price' => [
                                'price' => $record['semi_annually'],
                                'setup_fee' => $record['semi_annually_setup'],
                                'currency_code' => $this->currency_code,
                            ],
                        ], $common_fields));
                    }

                    if ($record['annually']) {
                        array_push($plans, array_merge([
                            'name' => 'Annually',
                            'billing_period' => 1,
                            'billing_unit' => 'year',
                            'price' => [
                                'price' => $record['annually'],
                                'setup_fee' => $record['annually_setup'],
                                'currency_code' => $this->currency_code,
                            ],
                        ], $common_fields));
                    }

                    if ($record['biennially']) {
                        array_push($plans, array_merge([
                            'name' => 'Biennially',
                            'billing_period' => 2,
                            'billing_unit' => 'year',
                            'price' => [
                                'price' => $record['biennially'],
                                'setup_fee' => $record['biennially_setup'],
                                'currency_code' => $this->currency_code,
                            ],
                        ], $common_fields));
                    }

                    if ($record['triennially']) {
                        array_push($plans, array_merge([
                            'name' => 'Triennially',
                            'billing_period' => 3,
                            'billing_unit' => 'year',
                            'price' => [
                                'price' => $record['triennially'],
                                'setup_fee' => $record['triennially_setup'],
                                'currency_code' => $this->currency_code,
                            ],
                        ], $common_fields));
                    }

                    $all_prices = [];
                    foreach ($plans as $plan) {
                        $price = $plan['price'];
                        // Unset the price from the plan array, so it can be inserted without errors
                        unset($plan['price']);
                        $plan_id = DB::table('plans')->insertGetId($plan);
                        $all_prices[] = array_merge([
                            'plan_id' => $plan_id,
                        ], $price);
                    }
                    DB::table('prices')->insert($all_prices);

                    continue;
                }
            }
        });
    }

    protected function migrateConfigOptionProducts()
    {
        $this->info('Migrating Config option products...');

        $this->migrateInBatch('configurable_options', 'SELECT c.id, c.name, c.type, c.order, c.hidden, c.group_id, g.name AS group_name, g.description AS group_description, g.products, c.created_at, c.updated_at FROM configurable_options as c JOIN configurable_option_groups as g ON c.group_id = g.id LIMIT :limit OFFSET :offset', function ($records) {
            $config_option_products = [];
            foreach ($records as $record) {

                $products = json_decode($record['products']);

                foreach ($products as $product_id) {
                    $config_option_products[] = [
                        'config_option_id' => $record['id'],
                        'product_id' => (int) $product_id,
                    ];
                }
            }

            DB::table('config_option_products')->insert($config_option_products);
        });
    }

    protected function migrateExtensions()
    {
        $this->info('Migrating Extensions...');
        $stmt = $this->pdo->query('SELECT * FROM extensions');
        $records = $stmt->fetchAll();

        $extensions = [];
        foreach ($records as $record) {
            try {
                $extension = ExtensionHelper::getExtension($record['type'], $record['name']);
            } catch (Throwable $th) {
                $ext_name = $record['name'];
                $this->warn("Not Migrating '$ext_name', Error: " . $th->getMessage());

                continue;
            }

            $extensions[] = [
                'id' => $record['id'],
                'name' => $record['display_name'] ?? $record['name'],
                'extension' => $record['name'],
                'type' => $record['type'],
                'enabled' => $record['enabled'],
            ];

            $stmt = $this->pdo->prepare('SELECT * FROM extension_settings WHERE `extension_id` = :id');
            $stmt->bindValue(':id', $record['id']);
            $stmt->execute();
            $old_ext_settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $ext_name = $record['name'];
            $ext_type = $record['type'];

            try {
                $extension_cfg = ExtensionHelper::getConfig($ext_type, $ext_name);
            } catch (Throwable $th) {
                $this->warn("Error while getting Extension '$ext_name', Not migrating ext settings: " . $th->getMessage());

                continue;
            }

            $extension_settings = [];
            foreach ($old_ext_settings as $old_ext_setting) {

                // If a setting was renamed in v1, you can probably put the old and new one here
                // the migrator may be able to move that setting
                $old_ext_setting['key'] = match (strtolower($old_ext_setting['key'])) {
                    'apikey' => 'api_key',
                    default => $old_ext_setting['key'],
                };

                $setting = array_filter($extension_cfg, fn ($ext) => $ext['name'] == $old_ext_setting['key']);
                $setting = array_merge(...$setting);

                // Check if the extension wants the setting to be encrypted or not
                if ($setting['encrypted'] ?? false) {
                    try {
                        // Check if the setting was already encrypted, if yes don't change it
                        Crypt::decryptString($old_ext_setting['value']);
                    } catch (Throwable $th) {
                        // Else, encrypt it
                        $old_ext_setting['value'] = Crypt::encryptString($old_ext_setting['value']);
                    }
                } else {
                    try {
                        $decrypted = Crypt::decryptString($old_ext_setting['value']);
                        // If the setting was encrypted, decrypted it
                        $old_ext_setting['value'] = $decrypted;
                    } catch (Throwable $th) {
                        // Else, do nothing
                    }
                }

                $extension_settings[] = [
                    'key' => $old_ext_setting['key'],
                    'value' => $old_ext_setting['value'],
                    'type' => $setting['database_type'] ?? 'string',
                    'settingable_type' => 'App\Models\Server',
                    'settingable_id' => $old_ext_setting['extension_id'],
                    'encrypted' => $setting['encrypted'] ?? false,
                    'created_at' => $old_ext_setting['created_at'],
                    'updated_at' => $old_ext_setting['updated_at'],
                ];
            }

            DB::table('settings')->insert($extension_settings);
        }

        DB::table('extensions')->insert($extensions);
        $this->info('Done.');
    }

    protected function migrateProducts()
    {
        $this->info('Migrating Products...');
        $this->migrateInBatch('products', 'SELECT * FROM products LIMIT :limit OFFSET :offset', function ($records) {
            $records = array_map(function ($record) {
                return [
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'slug' => Str::slug($record['name']),
                    'description' => $record['description'],

                    'category_id' => $record['category_id'],
                    'image' => $record['image'],
                    'stock' => $record['stock_enabled'] ? $record['stock'] : null,
                    'per_user_limit' => $record['limit'],
                    'allow_quantity' => match ($record['allow_quantity']) {
                        0 => 'disabled',
                        1 => 'separated',
                        2 => 'combined',
                        default => 'disabled'
                    },
                    'server_id' => $record['extension_id'],

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('products')->insert($records);
        });

        $this->info('Migrating Product Settings...');
        $this->migrateInBatch('product_settings', 'SELECT * FROM product_settings LIMIT :limit OFFSET :offset', function ($product_settings) {

            $records = [];
            foreach ($product_settings as $record) {
                try {
                    $extension = Server::findOrFail($record['extension']);
                } catch (Throwable $th) {
                    $extension = $record['extension'];
                    $this->warn("Error while getting Extension '$extension', Not migrating ext product settings: " . $th->getMessage());

                    continue;
                }

                $migratedOption = ExtensionHelper::call($extension, 'migrateOption', [
                    'key' => $record['name'],
                    'value' => $record['value'],
                ], mayFail: true);
                $records[] = [
                    'key' => $migratedOption['key'] ?? $record['name'],
                    'value' => $migratedOption['value'] ?? $record['value'],
                    'type' => $migratedOption['type'] ?? 'string',
                    'settingable_type' => 'App\Models\Product',
                    'settingable_id' => $record['product_id'],
                    'encrypted' => $migratedOption['encrypted'] ?? false,
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }
            DB::table('settings')->insert($records);
        });
    }

    protected function migrateProductUpgrades()
    {
        $this->info('Migrating Product Upgrades...');

        $this->migrateInBatch(
            'product_upgrades',
            'SELECT * FROM `product_upgrades` LIMIT :limit OFFSET :offset',
            function ($records) {
                $records = array_map(function ($record) {
                    return [
                        'id' => $record['id'],
                        'product_id' => $record['product_id'],
                        'upgrade_id' => $record['upgrade_product_id'],

                        'created_at' => $record['created_at'],
                        'updated_at' => $record['updated_at'],
                    ];
                }, $records);

                DB::table('product_upgrades')->insert($records);
            }
        );
    }

    protected function migrateServiceCancellations()
    {
        $this->info('Migrating Service Cancellations...');

        $this->migrateInBatch(
            'cancellations',
            'SELECT cancellations.*, order_products.status as service_status
            FROM `cancellations`
            LEFT JOIN `order_products` ON cancellations.order_product_id = order_products.id LIMIT :limit OFFSET :offset',
            function ($records) {

                $cancellations = [];
                foreach ($records as $record) {
                    if ($record['service_status'] === 'cancelled') {
                        continue;
                    }

                    $cancellations[] = [
                        'id' => $record['id'],
                        'service_id' => $record['order_product_id'],
                        'reason' => $record['reason'],
                        'type' => 'end_of_period',
                        'created_at' => $record['created_at'],
                        'updated_at' => $record['updated_at'],
                    ];
                }

                DB::table('service_cancellations')->insert($cancellations);
            }
        );
    }

    protected function migrateInvoices()
    {
        $this->info('Migrating Invoices, Invoice Items, and Invoice Transactions...');

        $this->migrateInBatch('invoices', 'SELECT * FROM invoices LIMIT :limit OFFSET :offset', function ($records) {
            $invoice_ids = implode(',', array_column($records, 'id'));
            $items_stmt = $this->pdo->prepare("SELECT
                invoice_items.*,
                order_products.id as service_id,
                order_products.quantity as service_quantity
            FROM
                invoice_items
            LEFT JOIN
                order_products ON invoice_items.product_id = order_products.id
            WHERE invoice_id IN($invoice_ids)
            ");
            $items_stmt->execute();
            $invoice_items_db = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

            $invoice_transactions = [];
            $invoice_items = [];

            $invoices = array_map(function ($record) use ($invoice_items_db, &$invoice_items, &$invoice_transactions) {
                $transaction_amount = 0;

                $items = array_map(function ($item) use (&$transaction_amount) {

                    $price = number_format((float) $item['total'], 2, '.', '');
                    $transaction_amount += (float) $price;

                    return [
                        'id' => $item['id'],
                        'invoice_id' => $item['invoice_id'],
                        'description' => $item['description'],
                        'price' => number_format((float) $item['total'], 2, '.', ''),
                        'quantity' => $item['service_quantity'] ?? 1,

                        'reference_type' => 'App\Models\Service',
                        'reference_id' => $item['service_id'],

                        'created_at' => $item['created_at'],
                        'updated_at' => $item['updated_at'],
                    ];
                }, array_filter($invoice_items_db, function ($item) use ($record) {
                    return $item['invoice_id'] === $record['id'];
                }));

                // Add the transaction details to invoice_transactions
                if ($transaction_amount > 0 && $record['status'] === 'paid') {
                    $gateway = Gateway::where('name', $record['paid_with'])->get()->first();
                    $invoice_transactions[] = [
                        'invoice_id' => $record['id'],
                        'transaction_id' => $record['paid_reference'],
                        'gateway_id' => $gateway ? $gateway->id : null,
                        'amount' => $transaction_amount,
                        'fee' => null,

                        'created_at' => $record['created_at'],
                        'updated_at' => $record['updated_at'],
                    ];
                }

                // Add the invoice items to invoice_items
                $invoice_items = array_merge($invoice_items, $items);

                return [
                    'id' => $record['id'],
                    'number' => $record['id'],
                    'status' => $record['status'],
                    'due_at' => $record['due_date'],
                    'currency_code' => $this->currency_code,
                    'user_id' => $record['user_id'],

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('invoices')->insert($invoices);
            DB::table('invoice_items')->insert($invoice_items);
            DB::table('invoice_transactions')->insert($invoice_transactions);

        });
        // Update settings for invoice number
        DB::table('settings')->updateOrInsert(
            ['key' => 'invoice_number'],
            ['value' => DB::table('invoices')->max('id') ?: 0]
        );
    }

    protected function migratePlans()
    {
        $this->info('Migrating Plans and Prices...');

        $stmt = $this->pdo->query('SELECT * FROM product_price');
        $records = $stmt->fetchAll();

        $plans = [];

        foreach ($records as $record) {

            $common_fields = [
                'type' => $record['type'],
                'priceable_id' => $record['product_id'],
                'priceable_type' => 'App\Models\Product',
            ];

            if ($record['monthly']) {
                array_push($plans, array_merge([
                    'name' => 'Monthly',
                    'billing_period' => 1,
                    'billing_unit' => 'month',
                    'price' => [
                        'price' => $record['monthly'],
                        'setup_fee' => $record['monthly_setup'],
                        'currency_code' => $this->currency_code,
                    ],
                ], $common_fields));
            }

            if ($record['quarterly']) {
                array_push($plans, array_merge([
                    'name' => 'Quarterly',
                    'billing_period' => 3,
                    'billing_unit' => 'month',
                    'price' => [
                        'price' => $record['quarterly'],
                        'setup_fee' => $record['quarterly_setup'],
                        'currency_code' => $this->currency_code,
                    ],
                ], $common_fields));
            }

            if ($record['semi_annually']) {
                array_push($plans, array_merge([
                    'name' => 'Semi-Annually',
                    'billing_period' => 6,
                    'billing_unit' => 'month',
                    'price' => [
                        'price' => $record['semi_annually'],
                        'setup_fee' => $record['semi_annually_setup'],
                        'currency_code' => $this->currency_code,
                    ],
                ], $common_fields));
            }

            if ($record['annually']) {
                array_push($plans, array_merge([
                    'name' => 'Annually',
                    'billing_period' => 1,
                    'billing_unit' => 'year',
                    'price' => [
                        'price' => $record['annually'],
                        'setup_fee' => $record['annually_setup'],
                        'currency_code' => $this->currency_code,
                    ],
                ], $common_fields));
            }

            if ($record['biennially']) {
                array_push($plans, array_merge([
                    'name' => 'Biennially',
                    'billing_period' => 2,
                    'billing_unit' => 'year',
                    'price' => [
                        'price' => $record['biennially'],
                        'setup_fee' => $record['biennially_setup'],
                        'currency_code' => $this->currency_code,
                    ],
                ], $common_fields));
            }

            if ($record['triennially']) {
                array_push($plans, array_merge([
                    'name' => 'Triennially',
                    'billing_period' => 3,
                    'billing_unit' => 'year',
                    'price' => [
                        'price' => $record['triennially'],
                        'setup_fee' => $record['triennially_setup'],
                        'currency_code' => $this->currency_code,
                    ],
                ], $common_fields));
            }
        }

        $all_prices = [];
        foreach ($plans as $plan) {
            $price = $plan['price'];
            // Unset the price from the plan array, so it can be inserted without errors
            unset($plan['price']);
            $plan_id = DB::table('plans')->insertGetId($plan);
            $all_prices[] = array_merge([
                'plan_id' => $plan_id,
            ], $price);
        }
        DB::table('prices')->insert($all_prices);

        $this->info('Done.');
    }

    protected function migrateOrdersAndServices()
    {
        $this->info('Migrating Orders...');
        $order_product_details = [];

        $this->migrateInBatch('orders', 'SELECT * FROM orders LIMIT :limit OFFSET :offset', function ($records) use (&$order_product_details) {
            $records = array_map(function ($record) use (&$order_product_details) {
                $order_product_details[$record['id']] = [
                    'coupon_id' => $record['coupon_id'],
                    'user_id' => $record['user_id'],
                ];

                return [
                    'id' => $record['id'],
                    'user_id' => $record['user_id'],
                    'currency_code' => $this->currency_code,

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('orders')->insert($records);
        });

        $this->info('Migrating Services...');
        $this->migrateInBatch('order_products', 'SELECT
            op.*,
            opc.value as stripe_subscription_id
        FROM
            order_products op
        LEFT JOIN
            order_products_config opc
            ON op.id = opc.order_product_id
            AND opc.key = \'stripe_subscription_id\'
        LIMIT :limit OFFSET :offset
        ', function ($records) use ($order_product_details) {
            $records = array_map(function ($record) use ($order_product_details) {
                $order = $order_product_details[$record['order_id']];

                $billing = match ($record['billing_cycle']) {
                    'monthly' => [
                        'type' => 'recurring',
                        'unit' => 'month',
                        'period' => 1,
                    ],
                    'quarterly' => [
                        'type' => 'recurring',
                        'unit' => 'month',
                        'period' => 3,
                    ],
                    'semi_annually' => [
                        'type' => 'recurring',
                        'unit' => 'month',
                        'period' => 6,
                    ],
                    'annually' => [
                        'type' => 'recurring',
                        'unit' => 'year',
                        'period' => 1,
                    ],
                    'biennially' => [
                        'type' => 'recurring',
                        'unit' => 'year',
                        'period' => 2,
                    ],
                    'triennially' => [
                        'type' => 'recurring',
                        'unit' => 'year',
                        'period' => 3,
                    ],
                    null => $record['price'] === 0 ? [
                        'type' => 'free',
                        'unit' => null,
                        'period' => null,
                    ] : [
                        'type' => 'one-time',
                        'unit' => null,
                        'period' => null,
                    ]
                };

                $price = Price::where('price', $record['price'])
                    ->whereHas('plan', function ($query) use ($billing) {
                        $query->where('priceable_type', 'App\Models\Product')
                            ->where('type', $billing['type'])
                            ->where('billing_period', $billing['period'])
                            ->where('billing_unit', $billing['unit']);
                    })->first();

                if (!$price) {
                    // Select the plan where the price doesn't match
                    $plan = DB::table('plans')
                        ->where('priceable_type', 'App\Models\Product')
                        ->where('type', $billing['type'])
                        ->where('billing_period', $billing['period'])
                        ->where('billing_unit', $billing['unit'])
                        ->first();
                    if ($plan) {
                        $plan_id = $plan->id;
                    } else {
                        // If the plan doesn't exist, create it
                        $this->warn("Price not found for order_product_id: {$record['id']}, Creating custom plan.");
                        $plan_id = DB::table('plans')->insertGetId([
                            'name' => "Custom - {$billing['unit']} {$billing['type']}",
                            'type' => $billing['type'],
                            'billing_period' => $billing['period'],
                            'billing_unit' => $billing['unit'],
                            'priceable_id' => $record['product_id'],
                            'priceable_type' => 'App\Models\Product',
                        ]);

                        DB::table('prices')->insert([
                            'plan_id' => $plan_id,
                            'price' => $record['price'],
                            'setup_fee' => null,
                            'currency_code' => $this->currency_code,
                        ]);
                    }
                }

                return [
                    'id' => $record['id'],
                    // Active instead of Paid status, leave rest unchanged
                    'status' => match ($record['status']) {
                        'paid' => 'active',
                        null => 'cancelled',
                        default => $record['status']
                    },
                    'order_id' => $record['order_id'],
                    'product_id' => $record['product_id'],
                    'user_id' => $order['user_id'],
                    'currency_code' => $this->currency_code,

                    'quantity' => $record['quantity'],
                    'price' => $record['price'],

                    'plan_id' => $price->plan_id ?? $plan_id,
                    'coupon_id' => $order['coupon_id'],
                    'expires_at' => $record['expiry_date'],
                    'subscription_id' => $record['stripe_subscription_id'],
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);
            DB::table('services')->insert($records);
        });
    }

    protected function migrateServiceConfigs()
    {
        $this->info('Migrating Service Configs...');
        $this->migrateInBatch('order_products_config', 'SELECT * FROM order_products_config LIMIT :limit OFFSET :offset', function ($records) {
            $service_properties = [];
            $service_configs = [];
            foreach ($records as $record) {
                if ($record['key'] === 'stripe_subscription_id') {
                    continue;
                }
                if ($record['is_configurable_option'] === 1) {
                    $configOption = ConfigOption::whereId($record['key'])->first();
                    if (!$configOption) {
                        $this->warn("Config option not found for order_product_id: {$record['order_product_id']}, key: {$record['key']}");

                        continue;
                    }
                    if (in_array($configOption->type, ['text', 'number'])) {
                        $service_properties[] = [
                            'name' => $record['key'],
                            'key' => $record['key'],
                            'custom_property_id' => null,
                            'model_id' => $record['order_product_id'],
                            'model_type' => 'App\Models\Service',
                            'value' => $record['value'],
                        ];

                        continue;
                    }
                    $service_configs[] = [
                        'configurable_type' => 'App\Models\Service',
                        'configurable_id' => $record['order_product_id'],
                        'config_option_id' => $configOption->id,
                        'config_value_id' => $record['value'],
                    ];
                } else {
                    $service_properties[] = [
                        'name' => $record['key'],
                        'key' => $record['key'],
                        'custom_property_id' => null,
                        'model_id' => $record['order_product_id'],
                        'model_type' => 'App\Models\Service',
                        'value' => $record['value'],
                    ];
                }
            }

            DB::table('service_configs')->insert($service_configs);
            DB::table('properties')->insert($service_properties);
        });
    }

    protected function migrateUsers()
    {
        $this->info('Migrating Users...');

        // Custom Properties for users
        $address = CustomProperty::where('model', 'App\Models\User')->where('key', 'address')->first();
        $city = CustomProperty::where('model', 'App\Models\User')->where('key', 'city')->first();
        $state = CustomProperty::where('model', 'App\Models\User')->where('key', 'state')->first();
        $zip = CustomProperty::where('model', 'App\Models\User')->where('key', 'zip')->first();
        $country = CustomProperty::where('model', 'App\Models\User')->where('key', 'country')->first();
        $phone = CustomProperty::where('model', 'App\Models\User')->where('key', 'phone')->first();
        $companyname = CustomProperty::where('model', 'App\Models\User')->where('key', 'company_name')->first();

        $this->migrateInBatch('users', 'SELECT * FROM users LIMIT :limit OFFSET :offset', function ($records) use (
            $address,
            $city,
            $state,
            $zip,
            $country,
            $phone,
            $companyname
        ) {
            $properties = [];
            $credits = [];

            $records = array_map(function ($record) use (
                &$properties,
                &$credits,
                $address,
                $city,
                $state,
                $zip,
                $country,
                $phone,
                $companyname
            ) {
                // User properties
                if ($record['address']) {
                    array_push($properties, [
                        'name' => $address->name,
                        'key' => $address->key,
                        'custom_property_id' => $address->id,
                        'model_id' => $record['id'],
                        'model_type' => 'App\Models\User',
                        'value' => $record['address'],
                    ]);
                }
                if ($record['city']) {
                    array_push($properties, [
                        'name' => $city->name,
                        'key' => $city->key,
                        'custom_property_id' => $city->id,
                        'model_id' => $record['id'],
                        'model_type' => 'App\Models\User',
                        'value' => $record['city'],
                    ]);
                }
                if ($record['state']) {
                    array_push($properties, [
                        'name' => $state->name,
                        'key' => $state->key,
                        'custom_property_id' => $state->id,
                        'model_id' => $record['id'],
                        'model_type' => 'App\Models\User',
                        'value' => $record['state'],
                    ]);
                }
                if ($record['zip']) {
                    array_push($properties, [
                        'name' => $zip->name,
                        'key' => $zip->key,
                        'custom_property_id' => $zip->id,
                        'model_id' => $record['id'],
                        'model_type' => 'App\Models\User',
                        'value' => $record['zip'],
                    ]);
                }
                if ($record['country']) {
                    array_push($properties, [
                        'name' => $country->name,
                        'key' => $country->key,
                        'custom_property_id' => $country->id,
                        'model_id' => $record['id'],
                        'model_type' => 'App\Models\User',
                        'value' => $record['country'],
                    ]);
                }
                if ($record['phone']) {
                    array_push($properties, [
                        'name' => $phone->name,
                        'key' => $phone->key,
                        'custom_property_id' => $phone->id,
                        'model_id' => $record['id'],
                        'model_type' => 'App\Models\User',
                        'value' => $record['phone'],
                    ]);
                }
                if ($record['companyname']) {
                    array_push($properties, [
                        'name' => $companyname->name,
                        'key' => $companyname->key,
                        'custom_property_id' => $companyname->id,
                        'model_id' => $record['id'],
                        'model_type' => 'App\Models\User',
                        'value' => $record['companyname'],
                    ]);
                }
                if ($record['credits']) {
                    array_push($credits, [
                        'user_id' => $record['id'],
                        'amount' => $record['credits'],
                        'currency_code' => $this->currency_code,
                        'created_at' => $record['created_at'],
                        'updated_at' => $record['updated_at'],
                    ]);
                }

                // User Details
                return [
                    'id' => $record['id'],
                    'first_name' => $record['first_name'],
                    'last_name' => $record['last_name'],
                    'email' => $record['email'],
                    // If the user had admin role, then give him admin, otherwise give no role
                    'role_id' => $record['role_id'] === 1 ? 1 : null,
                    'email_verified_at' => $record['email_verified_at'],
                    'password' => $record['password'],
                    'tfa_secret' => $record['tfa_secret'] ? Crypt::encryptString(Crypt::decrypt($record['tfa_secret'])) : null,
                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                    'remember_token' => $record['remember_token'],
                ];
            }, $records);

            DB::table('users')->insert($records);
            DB::table('properties')->insert($properties);
            DB::table('credits')->insert($credits);
        });
    }

    protected function migrateTickets()
    {
        $this->info('Migrating Tickets...');
        $this->migrateInBatch('tickets', 'SELECT * FROM tickets LIMIT :limit OFFSET :offset', function ($records) {
            $records = array_map(function ($record) {
                return [
                    'id' => $record['id'],
                    'subject' => $record['title'],
                    'status' => $record['status'],
                    'priority' => $record['priority'],
                    'department' => null,

                    'assigned_to' => $record['assigned_to'],
                    'user_id' => $record['user_id'],
                    'service_id' => $record['order_id'],

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('tickets')->insert($records);
        });
    }

    protected function migrateTicketMessages()
    {
        $this->info('Migrating Ticket Messages...');
        $this->migrateInBatch('ticket_messages', 'SELECT * FROM ticket_messages LIMIT :limit OFFSET :offset', function ($records) {

            $records = array_filter($records, fn ($record) => !is_null($record['message']) && $record['message'] !== '');

            $records = array_map(function ($record) {
                return [
                    'id' => $record['id'],
                    'ticket_id' => $record['ticket_id'],
                    'user_id' => $record['user_id'],
                    'message' => $record['message'],

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('ticket_messages')->insert($records);
        });
    }

    protected function migrateTaxRates()
    {
        $this->info('Migrating Tax Rates...');
        $this->migrateInBatch('tax_rates', 'SELECT * FROM tax_rates LIMIT :limit OFFSET :offset', function ($records) {
            $records = array_map(function ($record) {
                return [
                    'id' => $record['id'],
                    'name' => $record['name'],
                    'rate' => $record['rate'],
                    'country' => $record['country'],

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            // Country should be unique
            $records = array_filter($records, function ($record) {
                static $countries = [];
                if (in_array($record['country'], $countries)) {
                    $this->error("Duplicate country found: {$record['country']}," .
                        " Tax Rate with ID: {$record['id']} will not be migrated.");

                    return false;
                }
                $countries[] = $record['country'];

                return true;
            });

            DB::table('tax_rates')->insert($records);
        });
    }

    protected function migrateCategories()
    {
        $this->info('Migrating Categories...');
        $this->migrateInBatch('categories', 'SELECT * FROM categories LIMIT :limit OFFSET :offset', function ($records) {
            $records = array_map(function ($record) {
                return [

                    'id' => $record['id'],
                    'slug' => $record['slug'],
                    'name' => $record['name'],
                    'description' => $record['description'],
                    'image' => $record['image'],
                    'parent_id' => $record['category_id'],
                    'full_slug' => $record['slug'],

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('categories')->insert($records);
        });
    }

    protected function migrateCoupons()
    {
        $this->info('Migrating Coupons...');
        $this->migrateInBatch('coupons', 'SELECT * FROM coupons LIMIT :limit OFFSET :offset', function ($records) {
            $coupon_products = [];

            $records = array_map(function ($record) {
                if ($record['products']) {
                    foreach (json_decode($record['products']) as $product_id) {
                        $coupon_products[] = [
                            'coupon_id' => (int) $record['id'],
                            'product_id' => (int) $product_id,
                        ];
                    }
                }

                return [
                    'id' => $record['id'],

                    'type' => $record['type'] == 'percent' ? 'percentage' : 'fixed',
                    'recurring' => null,
                    'code' => $record['code'],
                    'value' => number_format((float) $record['value'], 2, '.', ''),
                    'max_uses' => (int) $record['max_uses'],
                    'starts_at' => $record['start_date'],
                    'expires_at' => $record['end_date'],

                    'created_at' => $record['created_at'],
                    'updated_at' => $record['updated_at'],
                ];
            }, $records);

            DB::table('coupons')->insert($records);
            DB::table('coupon_products')->insert($coupon_products);
        });
    }
}
