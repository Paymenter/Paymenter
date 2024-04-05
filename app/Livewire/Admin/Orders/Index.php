<?php

namespace App\Livewire\Admin\Orders;

use App\Helpers\ExtensionHelper;
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
        return Order::query()
            ->with(['user', 'products']);
    }

    public array $bulkActions = [
        'deleteOrders' => 'Delete Orders',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.orders.show', $row))
        ->setDefaultSort('id', 'desc');
    }

    public function deleteOrders(): void
    {
        $orders = Order::query()
            ->whereIn('id', $this->getSelected())
            ->with(['products', 'invoices'])
            ->get();

        foreach ($orders as $order) {
            foreach ($order->products()->get() as $product) {
                ExtensionHelper::terminateServer($product);
            }
            $order->products()->delete();
            $order->invoices()->delete();
            $order->delete();
        }


        $this->clearSelected();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('User', 'user.first_name')
                ->sortable()->searchable(),
            ComponentColumn::make('Total', 'id')
                ->component('money')
                ->attributes(fn ($_, $row) => [
                    'amount' => $row->total(),
                ]),
            Column::make('Created At', 'created_at')
                ->sortable()->searchable(),

        ];
    }
}
