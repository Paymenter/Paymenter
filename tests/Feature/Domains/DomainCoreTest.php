<?php

namespace Tests\Feature\Domains;

use App\Classes\Cart;
use App\Enums\DomainStatus;
use App\Events\Domain\Created;
use App\Events\Domain\Failed;
use App\Events\Domain\Transitioned;
use App\Events\Domain\Updated;
use App\Exceptions\RegistrarException;
use App\Helpers\ExtensionHelper;
use App\Models\CartItem;
use App\Models\Domain;
use App\Models\DomainNotice;
use App\Models\Server;
use App\Models\Service;
use App\Models\ServiceCancellation;
use App\Models\Tld;
use App\Models\TldPrice;
use App\Models\User;
use App\Services\Domain\DomainAvailability;
use App\Services\Domain\DomainCheckoutService;
use App\Services\Domain\DomainPricing;
use App\Services\Domain\DomainRenewalService;
use App\Services\Domain\DomainService;
use App\Services\Service\RenewServiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Paymenter\Extensions\Servers\FakeRegistrar\FakeRegistrar;
use Paymenter\Extensions\Servers\FakeServer\FakeServer;
use Tests\TestCase;

class DomainCoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        require_once base_path('tests/Fixtures/FakeRegistrar.php');
        require_once base_path('tests/Fixtures/FakeServer.php');
        FakeRegistrar::reset();
        FakeServer::$constructed = 0;
    }

    public function test_apply_writes_registrar_truth_to_domain_and_service(): void
    {
        [$service, $domain] = $this->domainService();

        DomainService::apply($domain, [
            'registrar_ref' => 'reg-1',
            'status' => 'active',
            'registered_at' => '2026-01-01',
            'expires_at' => '2028-01-01',
            'locked' => true,
            'nameservers' => ['ns1.example.com', 'ns2.example.com'],
            'privacy' => true,
        ], 'payment');

        $domain->refresh();
        $service->refresh();

        $this->assertSame(DomainStatus::Active, $domain->status);
        $this->assertSame('reg-1', $domain->registrar_ref);
        $this->assertSame('2028-01-01', $domain->expires_at->toDateString());
        $this->assertSame('2028-01-01', $service->expires_at->toDateString());
        $this->assertSame(Service::STATUS_ACTIVE, $service->status);
        $this->assertTrue($domain->locked);
        $this->assertSame(['ns1.example.com', 'ns2.example.com'], $domain->nameservers);
    }

    public function test_pricing_uses_exact_year_currency_and_fails_closed(): void
    {
        $tld = $this->tld();

        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'register' => 10.00,
            'renew' => 11.00,
            'transfer' => 9.00,
        ]);
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 2,
            'register' => 18.00,
            'renew' => 20.00,
            'transfer' => 17.00,
        ]);

        $twoYears = DomainPricing::forCheckout($tld, 'register', 2, 'USD');
        $this->assertTrue($twoYears->available);
        $this->assertSame(18.0, $twoYears->price);

        $missingYear = DomainPricing::forCheckout($tld, 'register', 3, 'USD');
        $this->assertFalse($missingYear->available);

        $missingCurrency = DomainPricing::forCheckout($tld, 'register', 2, 'EUR');
        $this->assertFalse($missingCurrency->available);

        $tld->update(['enabled' => false]);
        $disabled = DomainPricing::forCheckout($tld->fresh(), 'register', 2, 'USD');
        $this->assertFalse($disabled->available);
    }

    public function test_domain_cart_uses_term_total_with_billing_quantity_one(): void
    {
        session(['currency' => 'USD']);

        $tld = $this->tld();
        $product = $this->createProduct();
        $product->product->update(['server_id' => $tld->server_id]);

        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'register' => 150.00,
        ]);
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 2,
            'register' => 280.00,
        ]);

        $id = Cart::add($product->product, $product->plan, [], [
            'sld' => 'example',
            'tld' => 'com',
            'domain' => 'example.com',
            'years' => 2,
        ]);

        $item = CartItem::findOrFail($id);

        $this->assertSame(1, $item->quantity);
        $this->assertSame(280.0, (float) $item->price->total);
    }

    public function test_domain_checkout_reports_a_missing_registration_period(): void
    {
        $tld = $this->tld();
        $product = $this->createProduct();
        $product->product->update(['server_id' => $tld->server_id]);

        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'register' => 150.00,
        ]);

        $result = DomainCheckoutService::check($product->product, ['sld' => 'example', 'tld' => 'com', 'years' => 2], true);

        $this->assertFalse($result['available']);
        $this->assertSame('This registration period is not available.', $result['error']);
    }

    public function test_cancelled_domain_does_not_get_a_renewal_invoice(): void
    {
        [, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['status' => DomainStatus::Cancelled]);

        $this->assertNull(DomainRenewalService::createInvoice($domain->fresh()));
    }

    public function test_domain_renewal_invoice_uses_term_total_once(): void
    {
        [$service, $domain] = $this->domainService(withRegistrar: true);
        $service->properties()->updateOrCreate(['key' => 'years'], ['value' => '2']);
        $service->update(['quantity' => 1]);

        TldPrice::create([
            'tld_id' => $domain->tldRecord->id,
            'currency_code' => 'USD',
            'years' => 2,
            'renew' => 300.00,
        ]);

        $invoice = DomainRenewalService::createInvoice($domain->fresh());
        $item = $invoice->items->first();

        $this->assertSame(1, $item->quantity);
        $this->assertSame(300.0, (float) $item->price);
        $this->assertSame(300.0, (float) $invoice->total);
    }

    public function test_tld_resolution_uses_the_longest_catalogue_match(): void
    {
        $server = Server::create([
            'name' => 'Fake Registrar',
            'extension' => 'FakeRegistrar',
            'type' => 'server',
            'enabled' => true,
        ]);

        Tld::create(['tld' => 'co.za', 'server_id' => $server->id, 'enabled' => true]);
        Tld::create(['tld' => 'za', 'server_id' => $server->id, 'enabled' => true]);
        Tld::create(['tld' => 'com', 'server_id' => $server->id, 'enabled' => true]);

        $this->assertSame('co.za', DomainService::tldFromName('example.co.za', $server->id));
        $this->assertSame('com', DomainService::tldFromName('example.com', $server->id));
        $this->assertNull(DomainService::tldFromName('example.unknown', $server->id));
    }

    public function test_registrar_product_pricing_fails_closed_without_tld_or_price(): void
    {
        $server = Server::create([
            'name' => 'Fake Registrar',
            'extension' => 'FakeRegistrar',
            'type' => 'server',
            'enabled' => true,
        ]);

        $product = $this->createProduct();
        $product->product->update(['server_id' => $server->id]);

        $missingTld = DomainPricing::forProduct($product->product, [
            'domain_name' => 'example.co.za',
            'years' => 2,
        ], 'USD');

        $this->assertNotNull($missingTld);
        $this->assertFalse($missingTld->available);

        $tld = Tld::create(['tld' => 'co.za', 'server_id' => $server->id, 'enabled' => true]);

        $missingPrice = DomainPricing::forProduct($product->product, [
            'domain_name' => 'example.co.za',
            'years' => 2,
        ], 'USD');

        $this->assertNotNull($missingPrice);
        $this->assertFalse($missingPrice->available);

        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 2,
            'register' => 250.00,
            'renew' => 260.00,
            'transfer' => 240.00,
        ]);

        $priced = DomainPricing::forProduct($product->product, [
            'domain_name' => 'example.co.za',
            'years' => 2,
        ], 'USD');

        $this->assertTrue($priced->available);
        $this->assertSame(250.0, $priced->price);
    }

    public function test_domain_products_resolve_tlds_to_the_correct_registrar(): void
    {
        $serverA = Server::create(['name' => 'Registrar A', 'extension' => 'FakeRegistrar', 'type' => 'server', 'enabled' => true]);
        $serverB = Server::create(['name' => 'Registrar B', 'extension' => 'FakeRegistrar', 'type' => 'server', 'enabled' => true]);

        $productA = $this->createProduct();
        $productA->product->update(['server_id' => $serverA->id]);
        $productB = $this->createProduct();
        $productB->product->update(['server_id' => $serverB->id]);

        $com = Tld::create(['tld' => 'com', 'server_id' => $serverA->id, 'product_id' => $productA->product->id, 'enabled' => true]);
        $net = Tld::create(['tld' => 'net', 'server_id' => $serverB->id, 'product_id' => $productB->product->id, 'enabled' => true]);

        TldPrice::create(['tld_id' => $com->id, 'currency_code' => 'USD', 'years' => 1, 'register' => 10, 'renew' => 10, 'transfer' => 10]);
        TldPrice::create(['tld_id' => $net->id, 'currency_code' => 'USD', 'years' => 1, 'register' => 20, 'renew' => 20, 'transfer' => 20]);

        $this->assertSame(['com'], array_keys(DomainCheckoutService::fields($productA->product)[1]['options']));
        $this->assertSame(['net'], array_keys(DomainCheckoutService::fields($productB->product)[1]['options']));

        $this->assertSame(10.0, DomainPricing::forProduct($productA->product, ['sld' => 'example', 'tld' => 'com', 'years' => 1], 'USD')->price);
        $this->assertSame(20.0, DomainPricing::forProduct($productB->product, ['sld' => 'example', 'tld' => 'net', 'years' => 1], 'USD')->price);
        $this->assertFalse(DomainPricing::forProduct($productA->product, ['sld' => 'example', 'tld' => 'net', 'years' => 1], 'USD')->available);

        $this->assertTrue(DomainCheckoutService::check($productB->product, ['sld' => 'example', 'tld' => 'net', 'years' => 1], true)['available']);
        $this->assertFalse(DomainCheckoutService::check($productA->product, ['sld' => 'example', 'tld' => 'net', 'years' => 1], true)['available']);

        $multi = DomainCheckoutService::checkForProducts([$productA->product, $productB->product], [
            'sld' => 'example',
            'tld' => 'net',
            'years' => 1,
        ], true);

        $this->assertTrue($multi['available']);
        $this->assertSame($productB->product->id, $multi['domain_product_id']);
    }

    public function test_tld_sync_for_server_is_idempotent_and_preserves_sell_prices(): void
    {
        $tld = $this->tld();
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'cost' => 1.00,
            'register' => 12.00,
            'renew' => 13.00,
            'transfer' => 11.00,
        ]);

        DomainService::syncTldsForServer($tld->server);
        DomainService::syncTldsForServer($tld->server);

        $rows = TldPrice::where('tld_id', $tld->id)->where('currency_code', 'USD')->where('years', 1)->get();

        $this->assertCount(1, $rows);
        $this->assertSame(9.5, $rows->first()->cost);
        $this->assertSame(12.0, $rows->first()->register);
        $this->assertSame(13.0, $rows->first()->renew);
        $this->assertSame(11.0, $rows->first()->transfer);
    }

    public function test_backfill_for_server_creates_missing_domains_without_touching_service(): void
    {
        $tld = $this->tld();
        $product = $this->createProduct();
        $product->product->update(['server_id' => $tld->server_id]);

        $user = User::factory()->create();
        $service = Service::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'status' => Service::STATUS_ACTIVE,
            'expires_at' => now()->addYear(),
        ]);
        $service->properties()->create(['key' => 'domain', 'value' => 'example.com']);
        $service->properties()->create(['key' => 'tld', 'value' => 'com']);

        DomainService::backfillForServer($tld->server);
        DomainService::backfillForServer($tld->server);

        $domain = $service->fresh()->domain;

        $this->assertNotNull($domain);
        $this->assertSame('example.com', $domain->name);
        $this->assertSame($tld->id, $domain->tld_id);
        $this->assertSame(DomainStatus::Active, $domain->status);
        $this->assertSame('backfill', $domain->last_transition_reason);
        $this->assertSame(Service::STATUS_ACTIVE, $service->fresh()->status);
        $this->assertSame(1, Domain::where('service_id', $service->id)->count());
    }

    public function test_product_pricing_uses_the_tld_catalogue_for_registrar_products(): void
    {
        $tld = $this->tld();
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 2,
            'register' => 18.00,
            'renew' => 20.00,
            'transfer' => 17.00,
        ]);

        $product = $this->createProduct();
        $product->product->update(['server_id' => $tld->server_id]);

        $price = DomainPricing::forProduct($product->product, [
            'tld' => 'com',
            'domain' => 'example.com',
            'years' => 2,
        ], 'USD');

        $this->assertNotNull($price);
        $this->assertTrue($price->available);
        $this->assertSame(18.0, $price->price);

        $normalProduct = $this->createProduct();
        $this->assertNull(DomainPricing::forProduct($normalProduct->product, ['tld' => 'com'], 'USD'));
    }

    public function test_ensure_for_service_creates_a_pending_domain_from_service_properties(): void
    {
        [$service] = $this->domainService(withDomain: false, withRegistrar: true);

        $service->properties()->create(['key' => 'domain', 'value' => 'Example.COM']);
        $service->properties()->create(['key' => 'tld', 'value' => 'com']);

        $domain = DomainService::ensureForService($service);

        $this->assertNotNull($domain);
        $this->assertSame('example.com', $domain->name);
        $this->assertSame('com', $domain->tld);
        $this->assertSame(DomainStatus::Pending, $domain->status);
        $this->assertSame($service->id, $domain->service_id);
    }

    public function test_paid_pending_domain_registers_and_keeps_registrar_expiry(): void
    {
        [$service] = $this->domainService(status: Service::STATUS_PENDING, withDomain: false, withRegistrar: true);
        $service->properties()->create(['key' => 'domain', 'value' => 'example.com']);
        $service->properties()->create(['key' => 'tld', 'value' => 'com']);

        (new RenewServiceService)->handle($service->fresh());

        $service->refresh();
        $this->assertSame(1, FakeRegistrar::$registerCalls);
        $this->assertSame(Service::STATUS_ACTIVE, $service->status);
        $this->assertSame($service->domain->expires_at->toDateString(), $service->expires_at->toDateString());
    }

    public function test_active_domain_renewal_calls_registrar_and_advances_expiry(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['expires_at' => now()->addMonth()->toDateString()]);
        $service->update(['expires_at' => now()->addMonth()]);

        (new RenewServiceService)->handle($service->fresh());

        $this->assertSame(1, FakeRegistrar::$renewCalls);
        $this->assertSame(
            now()->addMonth()->addYears(2)->toDateString(),
            $domain->fresh()->expires_at->toDateString()
        );
    }

    public function test_abstract_registrar_registers_the_domain_and_activates_the_service(): void
    {
        [$service] = $this->domainService(withDomain: false, withRegistrar: true);
        $service->properties()->create(['key' => 'domain', 'value' => 'example.com']);
        $service->properties()->create(['key' => 'tld', 'value' => 'com']);

        ExtensionHelper::createServer($service);

        $service->refresh();
        $domain = $service->domain;

        $this->assertSame(1, FakeRegistrar::$registerCalls);
        $this->assertNotNull($domain);
        $this->assertSame('example.com', $domain->name);
        $this->assertSame(DomainStatus::Active, $domain->status);
        $this->assertSame(Service::STATUS_ACTIVE, $service->status);
        $this->assertSame($domain->expires_at->toDateString(), $service->expires_at->toDateString());
    }

    public function test_availability_check_delegates_to_the_registrar(): void
    {
        $tld = $this->tld();

        $result = DomainAvailability::check($tld, 'Example.COM');

        $this->assertTrue($result['available']);
        $this->assertFalse($result['premium']);
    }

    public function test_checkout_availability_validation_skips_non_registrar_and_transfers(): void
    {
        $normal = $this->createProduct();
        $this->assertNull(DomainAvailability::validateCheckout($normal->product, ['domain_name' => 'example.com']));

        $tld = $this->tld();
        $registrarProduct = $this->createProduct();
        $registrarProduct->product->update(['server_id' => $tld->server_id]);

        FakeRegistrar::$available = false;

        $this->assertNull(DomainAvailability::validateCheckout($registrarProduct->product, [
            'domain_name' => 'example.com',
            'mode' => 'transfer',
        ]));
    }

    public function test_checkout_availability_validation_blocks_unavailable_domain(): void
    {
        $tld = $this->tld();
        $product = $this->createProduct();
        $product->product->update(['server_id' => $tld->server_id]);

        FakeRegistrar::$available = false;

        $result = DomainAvailability::validateCheckout($product->product, [
            'domain_name' => 'example.com',
            'years' => 1,
        ]);

        $this->assertNotNull($result);
        $this->assertFalse($result['available']);
    }

    public function test_checkout_availability_validation_fails_closed_on_api_error_and_missing_tld(): void
    {
        $tld = $this->tld();
        $product = $this->createProduct();
        $product->product->update(['server_id' => $tld->server_id]);

        FakeRegistrar::$throwOnCheck = true;
        $this->assertFalse(DomainAvailability::validateCheckout($product->product, ['domain_name' => 'example.com'])['available']);

        FakeRegistrar::$throwOnCheck = false;
        $this->assertFalse(DomainAvailability::validateCheckout($product->product, ['domain_name' => 'example.unknown'])['available']);
    }

    public function test_domain_sync_applies_registrar_facts(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['expires_at' => '2020-01-01']);

        $this->artisan('app:domain-sync')->assertExitCode(0);

        $this->assertSame(now()->addYears(5)->toDateString(), $domain->fresh()->expires_at->toDateString());
        $this->assertNotNull($domain->fresh()->last_synced_at);
    }

    public function test_unchanged_sync_only_updates_the_sync_timestamp(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['expires_at' => '2028-01-01']);
        $service->update(['expires_at' => '2028-01-01']);

        Event::fake([Updated::class]);

        DomainService::apply($domain->fresh(), [
            'status' => 'active',
            'expires_at' => '2028-01-01',
        ], 'sync');

        Event::assertNotDispatched(Updated::class);
        $this->assertNotNull($domain->fresh()->last_synced_at);
    }

    public function test_domain_cancellation_disables_auto_renew_without_terminating_the_service(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['auto_renew' => true]);

        ServiceCancellation::create([
            'service_id' => $service->id,
            'type' => 'immediate',
            'reason' => 'test',
        ]);

        $this->assertFalse($domain->fresh()->auto_renew);
        $this->assertSame(Service::STATUS_ACTIVE, $service->fresh()->status);
    }

    public function test_suspend_cron_skips_services_that_have_a_domain(): void
    {
        [$service] = $this->domainService(status: Service::STATUS_ACTIVE);
        $service->update(['expires_at' => now()->subDays(10)]);

        $this->artisan('app:cron-job')->assertExitCode(0);

        $this->assertSame(Service::STATUS_ACTIVE, $service->fresh()->status);
    }

    public function test_terminate_cron_skips_services_that_have_a_domain(): void
    {
        [$service] = $this->domainService(status: Service::STATUS_SUSPENDED);
        $service->update(['expires_at' => now()->subDays(30)]);

        $this->artisan('app:cron-job')->assertExitCode(0);

        $this->assertSame(Service::STATUS_SUSPENDED, $service->fresh()->status);
    }

    public function test_invoice_cron_skips_domains_with_auto_renew_disabled(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE);
        $domain->update(['auto_renew' => false]);
        $service->update(['expires_at' => now()->addDays(2)]);

        config(['settings.cronjob_invoice' => 7, 'settings.cronjob_domain_invoice' => 30]);

        $this->artisan('app:cron-job')->assertExitCode(0);

        $this->assertDatabaseMissing('invoices', [
            'user_id' => $service->user_id,
            'status' => 'pending',
        ]);
    }

    public function test_status_change_dispatches_a_transitioned_event(): void
    {
        [, $domain] = $this->domainService(domainStatus: DomainStatus::Pending);
        Event::fake([Transitioned::class]);

        DomainService::apply($domain, ['status' => 'active'], 'payment');

        Event::assertDispatched(Transitioned::class, function (Transitioned $event) use ($domain) {
            return $event->domain->id === $domain->id
                && $event->from === DomainStatus::Pending
                && $event->to === DomainStatus::Active
                && $event->reason === 'payment';
        });
    }

    public function test_failure_dispatches_a_failed_event(): void
    {
        [, $domain] = $this->domainService();
        Event::fake([Failed::class]);

        DomainService::markFailed($domain, 'registrar exploded');

        Event::assertDispatched(Failed::class, function (Failed $event) use ($domain) {
            return $event->domain->id === $domain->id
                && $event->operation === 'registration'
                && $event->error === 'registrar exploded';
        });
    }

    public function test_premium_name_price_overrides_the_tld_catalogue(): void
    {
        $tld = $this->tld();
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 2,
            'register' => 18.00,
            'renew' => 20.00,
            'transfer' => 17.00,
        ]);

        FakeRegistrar::$premiumName = 'premium.com';
        FakeRegistrar::$premiumPrice = ['price' => 99.00, 'currency' => 'USD'];

        $premium = DomainPricing::forCheckout($tld, 'register', 2, 'USD', 'premium.com');
        $normal = DomainPricing::forCheckout($tld, 'register', 2, 'USD', 'normal.com');

        $this->assertTrue($premium->available);
        $this->assertSame(99.0, $premium->price);
        $this->assertSame(18.0, $normal->price);
    }

    public function test_premium_renewal_price_overrides_the_tld_catalogue(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['name' => 'premium.com']);
        $tld = $domain->tldRecord;
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 2,
            'register' => 18.00,
            'renew' => 20.00,
            'transfer' => 17.00,
        ]);

        FakeRegistrar::$premiumName = 'premium.com';
        FakeRegistrar::$premiumPrice = ['price' => 55.00, 'currency' => 'USD'];

        $price = DomainPricing::forRenewal($domain);

        $this->assertSame(55.0, $price->price);
    }

    public function test_tld_sync_imports_costs_without_overwriting_sell_prices(): void
    {
        $tld = $this->tld();
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'cost' => 1.00,
            'register' => 12.00,
            'renew' => 13.00,
            'transfer' => 11.00,
        ]);

        $this->artisan('app:domain-tlds')->assertExitCode(0);

        $price = TldPrice::where('tld_id', $tld->id)->where('currency_code', 'USD')->where('years', 1)->first();

        $this->assertSame(9.5, $price->cost);
        $this->assertSame(12.0, $price->register);
        $this->assertSame(13.0, $price->renew);
        $this->assertSame(11.0, $price->transfer);
    }

    public function test_domain_notices_are_sent_once_per_stage(): void
    {
        config(['settings.domain_notice_days' => '30,14,3']);

        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['status' => DomainStatus::Active, 'expires_at' => now()->addDays(30)]);

        $this->artisan('app:domain-notices')->assertExitCode(0);
        $this->artisan('app:domain-notices')->assertExitCode(0);

        $this->assertSame(1, DomainNotice::where('domain_id', $domain->id)->where('key', 'domain_renewal_upcoming')->count());
    }

    public function test_domain_notices_are_sent_again_for_a_new_renewal_cycle(): void
    {
        config(['settings.domain_notice_days' => '30']);

        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['expires_at' => now()->addDays(30)]);

        $this->artisan('app:domain-notices')->assertExitCode(0);

        $this->travel(1)->days();
        $domain->update(['expires_at' => now()->addDays(30)]);

        $this->artisan('app:domain-notices')->assertExitCode(0);

        $this->assertSame(2, DomainNotice::where('domain_id', $domain->id)->where('key', 'domain_renewal_upcoming')->count());
    }

    public function test_delete_at_registrar_cancels_a_redemption_domain(): void
    {
        [, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $domain->update(['status' => DomainStatus::Redemption]);

        $result = DomainService::deleteAtRegistrar($domain, 'admin:1');

        $this->assertTrue($result);
        $this->assertSame(1, FakeRegistrar::$deleteCalls);
        $this->assertSame(DomainStatus::Cancelled, $domain->fresh()->status);
    }

    public function test_delete_at_registrar_refuses_a_domain_that_is_not_in_redemption(): void
    {
        [, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);

        $this->expectException(RegistrarException::class);

        DomainService::deleteAtRegistrar($domain);
    }

    public function test_is_registrar_does_not_instantiate_extensions(): void
    {
        $registrar = Server::create(['name' => 'Registrar', 'extension' => 'FakeRegistrar', 'type' => 'server', 'enabled' => true]);
        $server = Server::create(['name' => 'Server', 'extension' => 'FakeServer', 'type' => 'server', 'enabled' => true]);

        $this->assertTrue(ExtensionHelper::isRegistrar($registrar));
        $this->assertSame(0, FakeRegistrar::$constructed);

        $this->assertFalse(ExtensionHelper::isRegistrar($server));
        $this->assertSame(0, FakeServer::$constructed);

        $this->assertNotNull(ExtensionHelper::getRegistrar($registrar));
        $this->assertSame(1, FakeRegistrar::$constructed);

        $this->assertNull(ExtensionHelper::getRegistrar($server));
        $this->assertSame(0, FakeServer::$constructed);
    }

    public function test_renewal_pricing_fails_closed_without_service_currency_or_tld(): void
    {
        $orphan = Domain::create(['name' => 'orphan.com', 'tld' => 'com', 'status' => DomainStatus::Active]);
        $this->assertSame(1, $orphan->years());
        $this->assertFalse(DomainPricing::forRenewal($orphan)->available);

        [, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        $tldId = $domain->tld_id;
        $domain->update(['tld_id' => null]);
        $this->assertFalse(DomainPricing::forRenewal($domain->fresh())->available);

        $domain->update(['tld_id' => $tldId]);
        TldPrice::where('tld_id', $tldId)->delete();
        $this->assertFalse(DomainPricing::forRenewal($domain->fresh())->available);
    }

    public function test_domain_registrar_uses_stored_server_and_never_falls_back(): void
    {
        $serverA = Server::create(['name' => 'A', 'extension' => 'FakeRegistrar', 'type' => 'server', 'enabled' => true]);
        $serverA->settings()->create(['key' => 'username', 'value' => 'a']);
        $serverB = Server::create(['name' => 'B', 'extension' => 'FakeRegistrar', 'type' => 'server', 'enabled' => true]);
        $serverB->settings()->create(['key' => 'username', 'value' => 'b']);

        $tld = Tld::create(['tld' => 'com', 'server_id' => $serverA->id, 'enabled' => true]);
        $product = $this->createProduct();
        $product->product->update(['server_id' => $serverA->id]);

        $service = Service::factory()->create([
            'user_id' => User::factory()->create()->id,
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'status' => Service::STATUS_ACTIVE,
        ]);
        $domain = Domain::create([
            'service_id' => $service->id,
            'server_id' => $serverA->id,
            'tld_id' => $tld->id,
            'name' => 'example.com',
            'tld' => 'com',
            'status' => DomainStatus::Active,
        ]);

        $product->product->update(['server_id' => $serverB->id]);
        $domain->update(['server_id' => $serverA->id]);

        $registrar = DomainService::registrarForDomain($domain->fresh());
        $this->assertNotNull($registrar);
        $this->assertSame('a', $registrar->config('username'));

        $domain->update(['server_id' => null]);
        $this->assertNull(DomainService::registrarForDomain($domain->fresh()));
    }

    public function test_renewal_failure_is_caught_and_domain_is_marked_failed(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_ACTIVE, withRegistrar: true);
        FakeRegistrar::$throwOnRenew = true;

        (new RenewServiceService)->handle($service->fresh());

        $this->assertSame(DomainStatus::Failed, $domain->fresh()->status);
        $this->assertSame(Service::STATUS_ACTIVE, $service->fresh()->status);
    }

    public function test_backfill_does_not_dispatch_domain_events(): void
    {
        $tld = $this->tld();
        $product = $this->createProduct();
        $product->product->update(['server_id' => $tld->server_id]);

        $service = Service::factory()->create([
            'user_id' => User::factory()->create()->id,
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'status' => Service::STATUS_ACTIVE,
        ]);
        $service->properties()->create(['key' => 'domain', 'value' => 'example.com']);
        $service->properties()->create(['key' => 'tld', 'value' => 'com']);

        Event::fake([Created::class, Updated::class]);

        DomainService::backfillForServer($tld->server);

        Event::assertNotDispatched(Created::class);
        Event::assertNotDispatched(Updated::class);
    }

    public function test_sync_preserves_manual_suspension_but_payment_activates(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_SUSPENDED, withRegistrar: true);

        DomainService::apply($domain, ['status' => 'active'], 'sync');
        $this->assertSame(Service::STATUS_SUSPENDED, $service->fresh()->status);

        DomainService::apply($domain->fresh(), ['status' => 'active'], 'payment');
        $this->assertSame(Service::STATUS_ACTIVE, $service->fresh()->status);
    }

    public function test_admin_update_activates_a_suspended_domain_service(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_SUSPENDED, withRegistrar: true);

        DomainService::apply($domain, ['status' => 'active'], 'admin:42');

        $this->assertSame(Service::STATUS_ACTIVE, $service->fresh()->status);
    }

    public function test_transfer_in_pending_does_not_activate_service(): void
    {
        [$service, $domain] = $this->domainService(status: Service::STATUS_PENDING, domainStatus: DomainStatus::Pending, withRegistrar: true);

        DomainService::apply($domain, ['status' => 'transfer_in_pending'], 'payment');

        $this->assertSame(Service::STATUS_PENDING, $service->fresh()->status);
    }

    public function test_invalid_facts_are_rejected_without_writing(): void
    {
        [, $domain] = $this->domainService(domainStatus: DomainStatus::Pending);

        foreach ([
            ['status' => 'bogus'],
            ['expires_at' => 'not-a-date'],
            ['nameservers' => 'ns1.example.com'],
            ['locked' => 'yes'],
        ] as $facts) {
            try {
                DomainService::apply($domain, $facts, 'sync');
                $this->fail('Expected RegistrarException for ' . json_encode($facts));
            } catch (RegistrarException) {
                $this->assertSame(DomainStatus::Pending, $domain->fresh()->status);
            }
        }
    }

    public function test_premium_price_exception_fails_closed(): void
    {
        $tld = $this->tld();
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'register' => 10.00,
            'renew' => 10.00,
            'transfer' => 10.00,
        ]);
        FakeRegistrar::$premiumName = 'premium.com';
        FakeRegistrar::$throwOnPrice = true;

        $price = DomainPricing::forCheckout($tld, 'register', 1, 'USD', 'premium.com');

        $this->assertFalse($price->available);
    }

    public function test_domain_audit_excludes_epp_and_contacts(): void
    {
        $property = new \ReflectionProperty(Domain::class, 'auditExclude');
        $exclude = $property->getValue(new Domain);

        $this->assertContains('epp_code', $exclude);
        $this->assertContains('contacts', $exclude);
    }

    public function test_cart_domain_summary_uses_full_domain_or_sld_and_tld(): void
    {
        $this->assertSame(
            ['domain' => 'example.co.za', 'years' => 2],
            DomainCheckoutService::summary(['domain' => 'Example.CO.ZA', 'years' => 2])
        );

        $this->assertSame(
            ['domain' => 'example.net', 'years' => 1],
            DomainCheckoutService::summary(['sld' => 'example', 'tld' => 'net'])
        );

        $this->assertNull(DomainCheckoutService::summary(['years' => 2]));
    }

    private function tld(): Tld
    {
        $server = Server::create([
            'name' => 'Fake Registrar',
            'extension' => 'FakeRegistrar',
            'type' => 'server',
            'enabled' => true,
        ]);

        return Tld::create([
            'tld' => 'com',
            'server_id' => $server->id,
            'enabled' => true,
        ]);
    }

    private function domainService(
        string $status = Service::STATUS_PENDING,
        bool $withDomain = true,
        bool $withRegistrar = false,
        DomainStatus $domainStatus = DomainStatus::Active,
    ): array {
        $product = $this->createProduct();
        $user = User::factory()->create();

        $serverId = null;
        $tld = null;
        if ($withRegistrar) {
            $tld = $this->tld();
            $serverId = $tld->server_id;
        }

        $product->product->update(['server_id' => $serverId]);

        $service = Service::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'status' => $status,
            'quantity' => 2,
            'expires_at' => now()->addYear(),
        ]);

        $domain = null;
        if ($withDomain) {
            $domain = Domain::create([
                'service_id' => $service->id,
                'server_id' => $serverId,
                'tld_id' => $tld?->id,
                'tld' => 'com',
                'name' => 'example.com',
                'status' => $domainStatus,
                'expires_at' => now()->addYear(),
            ]);
        }

        return [$service, $domain];
    }
}
