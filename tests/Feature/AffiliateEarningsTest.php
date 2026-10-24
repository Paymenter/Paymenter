<?php

namespace Tests\Feature;

use App\Models\Extension;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\Pages\EditAffiliate;
use Paymenter\Extensions\Others\Affiliates\Admin\Resources\AffiliateResource\RelationManagers\AffiliatesRelationManager;
use Paymenter\Extensions\Others\Affiliates\Affiliates;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate;
use Paymenter\Extensions\Others\Affiliates\Models\AffiliateOrder;
use Tests\TestCase;

class AffiliateEarningsTest extends TestCase
{
    // The extension's tables live outside the core migrations, and creating them inside
    // RefreshDatabase's transaction would commit it.
    use DatabaseMigrations;

    private Affiliate $affiliate;

    private object $product;

    protected function afterRefreshingDatabase()
    {
        $this->artisan('migrate', ['--path' => 'extensions/Others/Affiliates/database/migrations']);

        Extension::create(['name' => 'Affiliates', 'extension' => 'Affiliates', 'type' => 'other', 'enabled' => true])
            ->settings()->create(['key' => 'default_reward', 'value' => 10]);

        (new Affiliates)->boot();

        // Otherwise the next application to boot would load the extension without its tables
        $this->beforeApplicationDestroyed(fn () => Extension::query()->forceDelete());

        $this->product = $this->createProduct();
        $this->affiliate = Affiliate::create(['user_id' => User::factory()->create()->id, 'code' => 'AFFILIATE1', 'visitors' => 0]);
    }

    private function referOrder(float $amount, string $status = 'paid'): AffiliateOrder
    {
        $user = User::factory()->create();
        $order = Order::create(['user_id' => $user->id, 'currency_code' => 'USD']);
        $service = Service::factory()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'product_id' => $this->product->product->id,
            'plan_id' => $this->product->plan->id,
            'price' => $amount,
            'status' => 'active',
        ]);
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'status' => $status]);
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'price' => $amount,
            'quantity' => 1,
            'reference_id' => $service->id,
            'reference_type' => Service::class,
        ]);

        return AffiliateOrder::create(['order_id' => $order->id, 'affiliate_id' => $this->affiliate->id]);
    }

    public function test_each_order_reports_its_own_earnings(): void
    {
        $first = $this->referOrder(100);
        $second = $this->referOrder(50);
        $unpaid = $this->referOrder(30, 'pending');

        $this->assertSame(['US Dollar' => 10.0], $first->earnings);
        $this->assertSame(['US Dollar' => 5.0], $second->earnings);
        $this->assertSame([], $unpaid->earnings);
        $this->assertSame(['US Dollar' => 15.0], $this->affiliate->earnings);
    }

    public function test_earnings_queries_do_not_grow_with_orders(): void
    {
        $this->referOrder(100);
        $queriesForOneOrder = $this->countEarningsQueries();

        $this->referOrder(50);
        $this->referOrder(20);
        $this->assertLessThanOrEqual($queriesForOneOrder, $this->countEarningsQueries());
    }

    private function countEarningsQueries(): int
    {
        $affiliate = Affiliate::find($this->affiliate->id);

        DB::flushQueryLog();
        DB::enableQueryLog();
        $affiliate->earnings;
        DB::disableQueryLog();

        return count(DB::getQueryLog());
    }

    public function test_order_list_shows_each_orders_earnings(): void
    {
        $this->referOrder(100);
        $this->referOrder(50);

        $this->actingAs(User::factory()->create(['role_id' => 1]));
        Filament::setCurrentPanel('admin');

        Livewire::test(AffiliatesRelationManager::class, ['ownerRecord' => $this->affiliate, 'pageClass' => EditAffiliate::class])
            ->assertSee('US Dollar: 10')
            ->assertSee('US Dollar: 5')
            ->assertDontSee('US Dollar: 15');
    }
}
