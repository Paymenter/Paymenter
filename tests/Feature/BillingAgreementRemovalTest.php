<?php

namespace Tests\Feature;

use App\Admin\Resources\UserResource\Pages\ShowBillingAgreements;
use App\Livewire\Client\PaymentMethods;
use App\Mail\Mail;
use App\Models\BillingAgreement;
use App\Models\Gateway;
use App\Models\Service;
use App\Models\User;
use App\Services\BillingAgreement\RemoveBillingAgreementService;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail as MailFacade;
use Livewire\Livewire;
use Tests\TestCase;

class BillingAgreementRemovalTest extends TestCase
{
    use RefreshDatabase;

    private function createAgreement(User $user): BillingAgreement
    {
        $gateway = Gateway::create([
            'name' => 'PayPal',
            'extension' => 'PayPal',
            'type' => 'gateway',
            'enabled' => true,
        ]);

        return BillingAgreement::create([
            'user_id' => $user->id,
            'gateway_id' => $gateway->id,
            'name' => 'PayPal test@example.com',
            'external_reference' => 'vault-token',
        ]);
    }

    private function createService(User $user, ?BillingAgreement $billingAgreement, array $attributes = []): Service
    {
        $product = $this->createProduct();

        return Service::factory()->create(array_merge([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'active',
            'currency_code' => 'USD',
            'price' => 10.00,
            'billing_agreement_id' => $billingAgreement?->id,
        ], $attributes));
    }

    public function test_agreement_is_removed_when_the_gateway_fails(): void
    {
        // PayPal throws when it cannot get an access token
        Http::fake(['*' => Http::response('', 401)]);

        $user = User::factory()->create();
        $agreement = $this->createAgreement($user);
        $service = $this->createService($user, $agreement);

        $revoked = (new RemoveBillingAgreementService)->handle($agreement);

        $this->assertFalse($revoked);
        $this->assertSoftDeleted($agreement);
        $this->assertNull($service->fresh()->billing_agreement_id);
    }

    public function test_customer_can_remove_payment_method_when_the_gateway_fails(): void
    {
        Http::fake(['*' => Http::response('', 401)]);

        $user = User::factory()->create();
        $agreement = $this->createAgreement($user);

        // Called directly: the page's breadcrumb declares a global function, so it can only render once per process
        $this->actingAs($user);
        (new PaymentMethods)->removePaymentMethod($agreement->ulid);

        $this->assertSoftDeleted($agreement);
    }

    public function test_admin_can_remove_billing_agreement(): void
    {
        Http::fake(['*' => Http::response('', 401)]);

        $admin = User::factory()->create(['role_id' => 1]);
        $user = User::factory()->create();
        $agreement = $this->createAgreement($user);
        $service = $this->createService($user, $agreement);

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(ShowBillingAgreements::class, ['record' => $user->getRouteKey()])
            ->callAction(TestAction::make('remove')->table($agreement));

        $this->assertSoftDeleted($agreement);
        $this->assertNull($service->fresh()->billing_agreement_id);
    }

    public function test_cron_does_not_charge_a_removed_agreement(): void
    {
        MailFacade::fake();
        config(['settings.cronjob_invoice' => 7]);

        $user = User::factory()->create();
        $agreement = $this->createAgreement($user);
        // Removed before the fix, which left the id on the service
        $agreement->delete();

        $this->createService($user, $agreement, ['expires_at' => now()->addDays(2)]);

        $this->artisan('app:cron-job')->assertExitCode(0);

        $this->assertDatabaseHas('invoices', ['user_id' => $user->id, 'status' => 'pending']);

        MailFacade::assertNotQueued(Mail::class, fn (Mail $mail) => $mail->emailTemplate->key === 'invoice_payment_failed');
    }

    public function test_migration_clears_removed_agreements_from_services(): void
    {
        $user = User::factory()->create();
        $removed = $this->createAgreement($user);
        $kept = BillingAgreement::create([
            'user_id' => $user->id,
            'gateway_id' => $removed->gateway_id,
            'name' => 'PayPal other@example.com',
            'external_reference' => 'other-vault-token',
        ]);
        $removedService = $this->createService($user, $removed);
        $keptService = $this->createService($user, $kept);
        $removed->delete();

        $migration = require database_path('migrations/2026_10_16_120000_clear_removed_billing_agreements_from_services.php');
        $migration->up();

        $this->assertNull($removedService->fresh()->billing_agreement_id);
        $this->assertEquals($kept->id, $keptService->fresh()->billing_agreement_id);
    }
}
