<?php

namespace App\Admin\Pages;

use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Support\CsvExport;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DataExport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Data Export';

    protected static ?string $title = 'Data Export';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?int $navigationSort = 90;

    protected static string $routePath = 'data-export';

    protected string $view = 'admin.pages.data-export';

    public ?array $data = [];

    private const INVOICE_COLUMNS = [
        'invoice_number' => 'Invoice Number',
        'invoice_id' => 'Invoice ID',
        'customer_name' => 'Customer Name',
        'customer_email' => 'Customer Email',
        'country' => 'Country',
        'status' => 'Status',
        'issued_at' => 'Issued At',
        'due_at' => 'Due At',
        'currency' => 'Currency',
        'net_amount' => 'Net Amount',
        'vat_rate' => 'VAT Rate',
        'vat_amount' => 'VAT Amount',
        'gross_amount' => 'Gross Amount (incl. VAT)',
        'paid' => 'Paid',
        'remaining' => 'Remaining',
    ];

    private const TRANSACTION_COLUMNS = [
        'payment_id' => 'Payment ID',
        'invoice_number' => 'Invoice Number',
        'invoice_id' => 'Invoice ID',
        'customer_name' => 'Customer Name',
        'customer_email' => 'Customer Email',
        'country' => 'Country',
        'gateway' => 'Payment Gateway',
        'transaction_id' => 'Transaction ID',
        'status' => 'Status',
        'amount' => 'Amount',
        'refunded_amount' => 'Refunded Amount',
        'fee' => 'Fee',
        'currency' => 'Currency',
        'credit_transaction' => 'Credit Transaction',
        'created_at' => 'Created At',
    ];

    public function mount(): void
    {
        $type = array_key_first($this->availableTypes());

        $this->form->fill([
            'type' => $type,
            'columns' => array_keys($this->columnsFor($type)),
            'from' => null,
            'to' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Select::make('type')
                        ->label('Data to export')
                        ->options(fn () => $this->availableTypes())
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state): void {
                            $set('columns', array_keys($this->columnsFor($state)));
                        }),
                    DatePicker::make('from')
                        ->label('From date'),
                    DatePicker::make('to')
                        ->label('To date'),
                    CheckboxList::make('columns')
                        ->label('Columns to include')
                        ->options(fn (Get $get) => $this->columnsFor($get('type')))
                        ->columns(3)
                        ->required()
                        ->columnSpanFull(),
                ])
                    ->columns(2)
                    ->livewireSubmitHandler('export'),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->export()),
        ];
    }

    public function export()
    {
        $data = $this->form->getState();

        if ($data['from'] && $data['to'] && $data['from'] > $data['to']) {
            $this->addError('data.from', 'The start date must be before or equal to the end date.');

            return null;
        }

        $type = $data['type'];
        $this->authorizeExportType($type);
        $columns = array_values(array_intersect(
            $data['columns'] ?? [],
            array_keys($this->columnsFor($type)),
        ));

        if ($columns === []) {
            Notification::make()->title('Select at least one column.')->danger()->send();

            return null;
        }

        $filename = $type . '-' . now()->format('Y-m-d') . '.csv';

        return CsvExport::download($filename, array_map(
            fn (string $column) => $this->columnsFor($type)[$column],
            $columns,
        ), function ($output) use ($type, $columns, $data): void {
            if ($type === 'invoices') {
                $this->writeInvoices($output, $columns, $data);
            } else {
                $this->writeTransactions($output, $columns, $data);
            }
        });
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user && (
            $user->hasPermission('admin.invoices.viewAny')
            || $user->hasPermission('admin.invoice_transactions.viewAny')
        );
    }

    private function availableTypes(): array
    {
        $types = [];
        if (Auth::user()?->hasPermission('admin.invoices.viewAny')) {
            $types['invoices'] = 'Invoices';
        }
        if (Auth::user()?->hasPermission('admin.invoice_transactions.viewAny')) {
            $types['transactions'] = 'Transactions / Payments';
        }

        return $types;
    }

    private function columnsFor(?string $type): array
    {
        return match ($type) {
            'invoices' => self::INVOICE_COLUMNS,
            'transactions' => self::TRANSACTION_COLUMNS,
            default => [],
        };
    }

    private function authorizeExportType(string $type): void
    {
        $permission = match ($type) {
            'invoices' => 'admin.invoices.viewAny',
            'transactions' => 'admin.invoice_transactions.viewAny',
            default => null,
        };

        abort_unless($permission && Auth::user()?->hasPermission($permission), 403);
    }

    private function writeInvoices($output, array $columns, array $data): void
    {
        $query = Invoice::query()
            ->with(['user', 'currency', 'items', 'adjustmentNotes', 'transactions', 'snapshot']);

        $this->applyDateRange($query, $data);

        $query->chunkById(250, function ($invoices) use ($output, $columns): void {
            foreach ($invoices as $invoice) {
                $formattedTotal = $invoice->formattedTotal;
                $gross = (float) $formattedTotal->total;
                $vat = (float) $formattedTotal->total_tax;
                $remaining = (float) $invoice->current_balance;
                $properties = $invoice->user_properties ?? [];
                $values = [
                    'invoice_number' => $invoice->number,
                    'invoice_id' => $invoice->id,
                    'customer_name' => $invoice->user_name,
                    'customer_email' => $invoice->user?->email,
                    'country' => $properties['country'] ?? null,
                    'status' => $invoice->status,
                    'issued_at' => $invoice->created_at?->toDateString(),
                    'due_at' => $invoice->due_at?->toDateString(),
                    'currency' => $invoice->currency_code,
                    'net_amount' => number_format((float) $formattedTotal->subtotal, 2, '.', ''),
                    'vat_rate' => number_format($vat > 0 ? (float) ($invoice->tax?->rate ?? 0) : 0, 2, '.', ''),
                    'vat_amount' => number_format($vat, 2, '.', ''),
                    'gross_amount' => number_format($gross, 2, '.', ''),
                    'paid' => number_format($gross - $remaining, 2, '.', ''),
                    'remaining' => number_format($remaining, 2, '.', ''),
                ];

                CsvExport::writeRow($output, array_map(fn (string $column) => $values[$column], $columns));
            }
        });
    }

    private function writeTransactions($output, array $columns, array $data): void
    {
        $query = InvoiceTransaction::query()
            ->with(['invoice.user', 'invoice.currency', 'invoice.snapshot', 'gateway']);

        $this->applyDateRange($query, $data);

        $query->chunkById(250, function ($transactions) use ($output, $columns): void {
            foreach ($transactions as $transaction) {
                $invoice = $transaction->invoice;
                $properties = $invoice?->user_properties ?? [];
                $values = [
                    'payment_id' => $transaction->id,
                    'invoice_number' => $invoice?->number,
                    'invoice_id' => $transaction->invoice_id,
                    'customer_name' => $invoice?->user_name,
                    'customer_email' => $invoice?->user?->email,
                    'country' => $properties['country'] ?? null,
                    'gateway' => $transaction->gateway?->name,
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $transaction->status->value,
                    'amount' => number_format((float) $transaction->amount, 2, '.', ''),
                    'refunded_amount' => number_format((float) $transaction->refunded_amount, 2, '.', ''),
                    'fee' => number_format((float) $transaction->fee, 2, '.', ''),
                    'currency' => $invoice?->currency_code,
                    'credit_transaction' => $transaction->is_credit_transaction ? 'yes' : 'no',
                    'created_at' => $transaction->created_at?->toDateTimeString(),
                ];

                CsvExport::writeRow($output, array_map(fn (string $column) => $values[$column], $columns));
            }
        });
    }

    private function applyDateRange(Builder $query, array $data): void
    {
        $query
            ->when($data['from'] ?? null, fn (Builder $query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($data['to'] ?? null, fn (Builder $query, $to) => $query->whereDate('created_at', '<=', $to));
    }
}
