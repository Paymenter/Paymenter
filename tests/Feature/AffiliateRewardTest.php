<?php

namespace Tests\Feature;

use App\Events\Invoice\Paid;
use App\Models\Credit;
use App\Models\Extension;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Paymenter\Extensions\Others\Affiliates\Affiliates;
use Paymenter\Extensions\Others\Affiliates\Listeners\RewardAffiliate;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Paymenter\Extensions\Others\Affiliates\Models\AffiliateOrder;
use Tests\TestCase;

class AffiliateRewardTest extends TestCase
{
    // The extension's tables live outside the core migrations, and creating them inside
    // RefreshDatabase's transaction would commit it.
    use DatabaseMigrations;

    private User $affiliateUser;

    private Invoice $invoice;

    protected function afterRefreshingDatabase()
    {
        $this->artisan('migrate', ['--path' => 'extensions/Others/Affiliates/database/migrations']);

        Extension::create(['name' => 'Affiliates', 'extension' => 'Affiliates', 'type' => 'other', 'enabled' => true])
            ->settings()->create(['key' => 'default_reward', 'value' => 10]);

        (new Affiliates)->boot();

        // Otherwise the next application to boot would load the extension without its tables
        $this->beforeApplicationDestroyed(fn () => Extension::query()->forceDelete());

        $this->affiliateUser = User::factory()->create();
        $affiliate = Affiliate::create(['user_id' => $this->affiliateUser->id, 'code' => 'AFFILIATE1', 'visitors' => 0]);

        $product = $this->createProduct();
        $customer = User::factory()->create();
        $order = Order::create(['user_id' => $customer->id, 'currency_code' => 'USD']);
        $service = Service::factory()->create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'price' => 100,
            'status' => 'active',
        ]);
        $this->invoice = Invoice::factory()->create(['user_id' => $customer->id, 'status' => 'paid']);
        InvoiceItem::factory()->create([
            'invoice_id' => $this->invoice->id,
            'price' => 100,
            'quantity' => 1,
            'reference_id' => $service->id,
            'reference_type' => Service::class,
        ]);
        AffiliateOrder::create(['order_id' => $order->id, 'affiliate_id' => $affiliate->id]);
    }

    private function reward(): void
    {
        (new RewardAffiliate)->handle(new Paid($this->invoice));
    }

    public function test_reward_creates_credit(): void
    {
        $this->reward();

        $this->assertEquals(10, $this->affiliateUser->credits()->first()->amount);
    }

    public function test_reward_is_added_through_the_model(): void
    {
        $this->affiliateUser->credits()->create(['currency_code' => 'USD', 'amount' => 5]);

        $updated = false;
        Event::listen('eloquent.updated: ' . Credit::class, function () use (&$updated) {
            $updated = true;
        });

        $this->reward();

        $this->assertEquals(15, $this->affiliateUser->credits()->first()->amount);
        $this->assertTrue($updated);
    }

    public function test_reward_keeps_a_concurrent_balance_change(): void
    {
        $credit = $this->affiliateUser->credits()->create(['currency_code' => 'USD', 'amount' => 5]);

        // Another request adds 20 between the listener reading the balance and writing it
        $changed = false;
        Event::listen('eloquent.retrieved: ' . Credit::class, function () use (&$changed, $credit) {
            if (!$changed) {
                $changed = true;
                Credit::whereKey($credit->id)->update(['amount' => 25]);
            }
        });

        $this->reward();

        $this->assertEquals(35, $credit->fresh()->amount);
    }
}
