<?php

namespace Tests\Feature;

use App\Helpers\ExtensionHelper;
use App\Models\Extension;
use App\Models\Gateway;
use App\Models\Server;
use App\Models\Setting;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExtensionBootSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function setting(Extension $owner, string $key, string $value): void
    {
        // Saved the way the admin pages save them: under the record's own morph class
        Setting::create([
            'key' => $key,
            'value' => $value,
            'settingable_type' => $owner->getMorphClass(),
            'settingable_id' => $owner->id,
        ]);
    }

    /**
     * config() works out the model to query from its caller, so read it from inside the extension.
     */
    private function readConfig(object $instance, string $key): mixed
    {
        return Closure::bind(fn () => $this->config($key), $instance, get_class($instance))();
    }

    public function test_gateways_boot_with_the_settings_saved_on_their_page(): void
    {
        $gateway = Gateway::create(['name' => 'Stripe', 'extension' => 'Stripe', 'type' => 'gateway', 'enabled' => false]);
        $this->setting($gateway, 'stripe_secret_key', 'sk_test_123');

        $booted = ExtensionHelper::bootable()->firstWhere('extension', 'Stripe');
        $this->assertNotNull($booted);

        $instance = ExtensionHelper::getExtension($booted->type, $booted->extension, $booted->settings);

        DB::enableQueryLog();
        $this->assertSame('sk_test_123', $this->readConfig($instance, 'stripe_secret_key'));
        $this->assertCount(0, DB::getQueryLog());
    }

    public function test_servers_boot_with_their_own_settings(): void
    {
        $server = Server::create(['name' => 'Panel', 'extension' => 'Pterodactyl', 'type' => 'server', 'enabled' => false]);
        $this->setting($server, 'host', 'https://panel.test');

        $booted = ExtensionHelper::bootable()->firstWhere('extension', 'Pterodactyl');

        $this->assertSame(['host' => 'https://panel.test'], $booted->settings->pluck('value', 'key')->all());
    }

    public function test_only_enabled_other_extensions_boot_once_per_class(): void
    {
        Extension::create(['name' => 'Off', 'extension' => 'Announcements', 'type' => 'other', 'enabled' => false]);
        $on = Extension::create(['name' => 'On', 'extension' => 'DiscordNotifications', 'type' => 'other', 'enabled' => true]);
        $first = Gateway::create(['name' => 'Stripe A', 'extension' => 'Stripe', 'type' => 'gateway', 'enabled' => true]);
        Gateway::create(['name' => 'Stripe B', 'extension' => 'Stripe', 'type' => 'gateway', 'enabled' => true]);

        $booted = ExtensionHelper::bootable();

        $this->assertNull($booted->firstWhere('extension', 'Announcements'));
        $this->assertSame($on->id, $booted->firstWhere('extension', 'DiscordNotifications')->id);
        $this->assertCount(1, $booted->where('extension', 'Stripe'));
        $this->assertSame($first->id, $booted->firstWhere('extension', 'Stripe')->id);
    }

    public function test_an_extension_without_saved_settings_does_not_query_on_every_read(): void
    {
        $extension = Extension::create(['name' => 'Affiliates', 'extension' => 'Affiliates', 'type' => 'other', 'enabled' => true]);

        // Built from its (empty) settings rows: nothing left to look up
        $booted = ExtensionHelper::getExtension('other', 'Affiliates', $extension->settings);
        DB::enableQueryLog();
        $this->readConfig($booted, 'type');
        $this->readConfig($booted, 'reward');
        $this->assertCount(0, DB::getQueryLog());

        // Built without settings: looked up once
        $fresh = ExtensionHelper::getExtension('other', 'Affiliates');
        DB::flushQueryLog();
        $this->readConfig($fresh, 'type');
        $this->readConfig($fresh, 'reward');
        $this->readConfig($fresh, 'cookie_max_age');
        $this->assertCount(2, DB::getQueryLog());
    }
}
