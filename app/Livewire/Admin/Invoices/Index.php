<?php

namespace App\Livewire\Admin\Invoices;

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
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
        return Invoice::query()
            ->with(['items.product.order.coupon', 'items.product.product', 'user']);
    }

    public array $bulkActions = [
        'deleteInvoices' => 'Delete Invoices',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
             ->setTableRowUrl(fn ($row) => route('admin.invoices.show', $row))
             ->setDefaultSort('id', 'desc');
    }

    public function deleteInvoices(): void
    {
        $invoices = Invoice::query()
            ->whereIn('id', $this->getSelected())
            ->with(['items'])
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->items()->delete();
            $invoice->delete();
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
            Column::make('Paid At', 'paid_at')
                ->sortable()->searchable(),
        ];
    }
}
