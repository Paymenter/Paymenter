<?php

namespace App\Admin\Resources\InvoiceTransactions;

use App\Admin\Clusters\InvoiceCluster;
use App\Admin\Resources\InvoiceTransactions\Pages\ListInvoiceTransactions;
use App\Admin\Resources\InvoiceTransactions\Tables\InvoiceTransactionsTable;
use App\Models\InvoiceTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class InvoiceTransactionResource extends Resource
{
    protected static ?string $model = InvoiceTransaction::class;

    protected static ?string $cluster = InvoiceCluster::class;

    public static function getNavigationLabel(): string
    {
        return __('invoices.invoice_transactions');
    }

    public static function getModelLabel(): string
    {
        return __('invoices.invoice_transaction_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('invoices.invoice_transactions_plural_label');
    }

    protected static string|BackedEnum|null $navigationIcon = 'ri-wallet-2-line';

    protected static string|BackedEnum|null $activeNavigationIcon = 'ri-wallet-2-fill';

    protected static ?string $recordTitleAttribute = 'transaction_id';

    public static function table(Table $table): Table
    {
        return InvoiceTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoiceTransactions::route('/'),
        ];
    }
}
