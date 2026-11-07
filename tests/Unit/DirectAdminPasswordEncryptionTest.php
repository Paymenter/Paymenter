<?php

namespace Tests\Unit;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Servers\DirectAdmin\DirectAdmin;
use Tests\TestCase;

class DirectAdminPasswordEncryptionTest extends TestCase
{
    use RefreshDatabase;

    private array $basicAuth = [];

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(function (Request $request) {
            $this->basicAuth[] = base64_decode(substr($request->header('Authorization')[0] ?? '', 6));

            return str_contains($request->url(), 'CMD_API_LOGIN_KEYS')
                ? Http::response('error=0&details=https://da.example.com/login-key')
                : Http::response('error=0&text=ok');
        });
    }

    private function extension(): DirectAdmin
    {
        return new DirectAdmin(['host' => 'https://da.example.com:2222', 'username' => 'admin', 'password' => 'admin-pass']);
    }

    private function service(): Service
    {
        $product = $this->createProduct();

        return Service::factory()->create([
            'user_id' => User::factory()->create()->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'currency_code' => 'USD',
        ]);
    }

    public function test_the_account_password_is_stored_encrypted(): void
    {
        $service = $this->service();

        $result = $this->extension()->createServer($service, ['package' => 'basic', 'ip' => '192.0.2.10'], ['domain' => 'example.com']);

        $stored = $service->properties()->where('key', 'directadmin_password')->value('value');
        $this->assertNotSame($result['password'], $stored);
        $this->assertSame($result['password'], Crypt::decryptString($stored));
    }

    public function test_sso_link_uses_the_decrypted_password(): void
    {
        $link = $this->extension()->ssoLink($this->service(), [], [
            'directadmin_username' => 'client1',
            'directadmin_password' => Crypt::encryptString('client-pass'),
        ]);

        $this->assertSame('https://da.example.com/login-key', $link);
        $this->assertSame('client1:client-pass', end($this->basicAuth));
    }

    public function test_sso_link_still_works_with_plain_text_passwords(): void
    {
        $this->extension()->ssoLink($this->service(), [], [
            'directadmin_username' => 'client2',
            'directadmin_password' => 'legacy-plain',
        ]);

        $this->assertSame('client2:legacy-plain', end($this->basicAuth));
    }

    public function test_migration_encrypts_existing_plain_text_passwords(): void
    {
        $service = $this->service();
        $plain = $service->properties()->create(['key' => 'directadmin_password', 'name' => 'DirectAdmin password', 'value' => 'old-plain']);
        $other = $service->properties()->create(['key' => 'directadmin_username', 'name' => 'DirectAdmin username', 'value' => 'client3']);

        $migration = require database_path('migrations/2026_10_16_000000_encrypt_directadmin_passwords.php');
        $migration->up();
        $migration->up(); // idempotent

        $value = DB::table('properties')->where('id', $plain->id)->value('value');
        $this->assertSame('old-plain', Crypt::decryptString($value));
        $this->assertSame('client3', DB::table('properties')->where('id', $other->id)->value('value'));

        $migration->down();
        $this->assertSame('old-plain', DB::table('properties')->where('id', $plain->id)->value('value'));
    }
}
