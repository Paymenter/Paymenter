<?php

namespace Tests\Unit;

use App\Admin\Resources\GatewayResource\Pages\CreateGateway;
use App\Admin\Resources\ServerResource\Pages\CreateServer;
use App\Helpers\ExtensionHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExtensionSettingsEncryptionTest extends TestCase
{
    use RefreshDatabase;

    private function create(string $page, array $data)
    {
        return (fn () => $this->handleRecordCreation($data))->call(new $page);
    }

    private function rawSetting($record, string $key): object
    {
        return DB::table('settings')
            ->where('settingable_type', $record->getMorphClass())
            ->where('settingable_id', $record->id)
            ->where('key', $key)
            ->first();
    }

    public function test_server_secrets_are_encrypted_when_the_server_is_created(): void
    {
        $server = $this->create(CreateServer::class, [
            'name' => 'DirectAdmin',
            'extension' => 'DirectAdmin',
            'type' => 'server',
            'settings' => ['host' => 'https://da.example.com:2222', 'username' => 'admin', 'password' => 'super-secret'],
        ]);

        $password = $this->rawSetting($server, 'password');
        $this->assertTrue((bool) $password->encrypted);
        $this->assertStringNotContainsString('super-secret', $password->value);

        $host = $this->rawSetting($server, 'host');
        $this->assertFalse((bool) $host->encrypted);
        $this->assertSame('https://da.example.com:2222', $host->value);

        // The extension still receives the plain value
        $extension = ExtensionHelper::getExtension('server', 'DirectAdmin', $server->fresh()->settings);
        $this->assertSame('super-secret', $extension->config('password'));
    }

    public function test_gateway_secrets_are_encrypted_when_the_gateway_is_created(): void
    {
        $gateway = $this->create(CreateGateway::class, [
            'name' => 'Stripe',
            'extension' => 'Stripe',
            'type' => 'gateway',
            'settings' => ['stripe_secret_key' => 'sk_test_secret', 'stripe_publishable_key' => 'pk_test_public'],
        ]);

        $secret = $this->rawSetting($gateway, 'stripe_secret_key');
        $this->assertTrue((bool) $secret->encrypted);
        $this->assertStringNotContainsString('sk_test_secret', $secret->value);

        $this->assertSame('sk_test_secret', $gateway->fresh()->settings->firstWhere('key', 'stripe_secret_key')->value);
    }
}
