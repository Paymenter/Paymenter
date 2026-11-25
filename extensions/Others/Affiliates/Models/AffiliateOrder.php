<?php

namespace Paymenter\Extensions\Others\Affiliates\Models;

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateOrder extends Model
{
    protected $table = 'ext_affiliate_orders';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'affiliate_id',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the earnings made from this order
     */
    public function earnings(): Attribute
    {
        return Attribute::make(
            get: fn (): array => self::earningsFor(new Collection([$this]))[$this->getKey()] ?? [],
        )->shouldCache();
    }

    /**
     * Earnings of many affiliate orders at once, keyed by affiliate order id.
     * The paid invoices are loaded in one query, with what Invoice::total reads.
     *
     * @param  Collection<int, AffiliateOrder>  $orders
     * @return array<int, array<string, float>>
     */
    public static function earningsFor(Collection $orders): array
    {
        if ($orders->isEmpty()) {
            return [];
        }

        $orders->loadMissing(['affiliate', 'order.services.invoiceItems']);

        $invoiceIds = [];
        foreach ($orders as $order) {
            $invoiceIds[$order->getKey()] = $order->order === null ? [] : $order->order->services
                ->flatMap(fn ($service) => $service->invoiceItems->pluck('invoice_id'))
                ->unique()
                ->all();
        }

        $invoices = Invoice::whereIn('id', array_unique(array_merge(...array_values($invoiceIds))))
            ->where('status', 'paid')
            ->with(['currency', 'items', 'adjustmentNotes'])
            ->get()
            ->keyBy('id');

        $defaultReward = null;
        $result = [];

        foreach ($orders as $order) {
            $reward_percentage = $order->affiliate?->reward
                ?: ($defaultReward ??= ExtensionHelper::getExtension('other', 'Affiliates')->config('default_reward'));

            $earnings = [];
            foreach ($invoiceIds[$order->getKey()] as $invoiceId) {
                $invoice = $invoices->get($invoiceId);
                if ($invoice === null) {
                    continue;
                }

                if (!isset($earnings[$invoice->currency->name])) {
                    $earnings[$invoice->currency->name] = 0;
                }
                $earnings[$invoice->currency->name] += $invoice->total * $reward_percentage / 100;
            }

            // Round to 2 decimal places
            $result[$order->getKey()] = array_map(fn ($total) => round($total, 2), $earnings);
        }

        return $result;
    }
}
