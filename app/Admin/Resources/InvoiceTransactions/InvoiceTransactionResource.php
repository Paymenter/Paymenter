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
