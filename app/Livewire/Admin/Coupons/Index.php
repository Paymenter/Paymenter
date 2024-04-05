<?php

namespace App\Livewire\Admin\Coupons;

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Coupon;
use App\Models\Order;
use App\Traits\Tables\DesignTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ComponentColumn;

class Index extends DataTableComponent
{
    use DesignTrait;

    public function builder(): Builder
    {
        return Coupon::query();
    }

    public array $bulkActions = [
        'deleteCoupons' => 'Delete Coupons',
    ];

    public function deleteCoupons(): void
    {
        $coupons = Coupon::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach (Order::query()->whereIn('coupon_id', $this->getSelected())->get() as $order) {
            $order->coupon_id = null;
            $order->save();
        }

        foreach ($coupons as $coupon) {
            $coupon->delete();
        }

        $this->clearSelected();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.coupons.edit', $row));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('Code', 'code')
                ->sortable()->searchable(),
            Column::make('Type', 'type')
                ->sortable()->searchable(),
            Column::make('Value', 'value')
                ->sortable()->searchable(),
            Column::make('Uses', 'uses')
                ->sortable()->searchable(),
            Column::make('Created At', 'created_at')
                ->sortable()->searchable(),
        ];
    }
}
