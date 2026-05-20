<?php

namespace App\Admin\Resources;

use App\Admin\Components\UserComponent;
use App\Admin\Resources\TicketResource\Pages\CreateTicket;
use App\Admin\Resources\TicketResource\Pages\EditTicket;
use App\Admin\Resources\TicketResource\Pages\ListTickets;
use App\Admin\Resources\TicketResource\Widgets\TicketsOverView;
use App\Models\Ticket;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-customer-service-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-customer-service-fill';

    public static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'open')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['subject'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->subject;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->label(__('ticket.subject'))
                    ->columnSpan(function ($record) {
                        return $record ? 2 : 1;
                    })
                    ->required(),
                Select::make('status')
                    ->label(__('ticket.status'))
                    ->options([
                        'open' => __('ticket.open'),
                        'closed' => __('ticket.closed'),
                        'replied' => __('ticket.replied'),
                    ])
                    ->columnSpan(function ($record) {
                        return $record ? 2 : 1;
                    })
                    ->default('open')
                    ->required(),
                Select::make('priority')
                    ->label(__('ticket.priority'))
                    ->options([
                        'low' => __('ticket.low'),
                        'medium' => __('ticket.medium'),
                        'high' => __('ticket.high'),
                    ])
                    ->columnSpan(function ($record) {
                        return $record ? 2 : 1;
                    })
                    ->default('medium')
                    ->required(),
                Select::make('department')
                    ->label(__('ticket.department'))
                    ->options(array_combine(config('settings.ticket_departments'), config('settings.ticket_departments')))
                    ->columnSpan(function ($record) {
                        return $record ? 2 : 1;
                    }),
                UserComponent::make('user_id')->columnSpan(function ($record) {
                    return $record ? 2 : 1;
                }),
                Select::make('assigned_to')
                    ->label(__('ticket.assigned_to'))
                    ->searchable()
                    ->preload()
                    ->relationship('user', 'id', fn (Builder $query) => $query->where('role_id', '!=', null))
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->columnSpan(function ($record) {
                        return $record ? 2 : 1;
                    }),
                Select::make('service_id')
                    ->label(__('ticket.service'))
                    ->relationship('service', 'id', function (Builder $query, Get $get) {
                        $query->where('user_id', $get('user_id'));
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->product->name} - " . ucfirst($record->status))
                    ->columnSpan(function ($record) {
                        return $record ? 2 : 1;
                    })
                    ->disabled(fn (Get $get) => !$get('user_id')),
                MarkdownEditor::make('message')
                    ->columnSpan(2)
                    ->label(__('ticket.initial_message'))
                    ->hiddenOn('edit')
                    ->columnSpan(function ($record) {
                        return $record ? 2 : 1;
                    })
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('subject')
                    ->label(__('ticket.subject'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('ticket.status'))
                    ->sortable()
                    ->badge()
                    ->color(fn (Ticket $record) => match ($record->status) {
                        'open' => 'success',
                        'closed' => 'danger',
                        'replied' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => __('ticket.' . $state)),
                TextColumn::make('priority')
                    ->label(__('ticket.priority'))
                    ->sortable()
                    ->badge()
                    ->color(fn (Ticket $record) => match ($record->priority) {
                        'low' => 'success',
                        'medium' => 'gray',
                        'high' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state) => __('ticket.' . $state)),
                TextColumn::make('department')
                    ->label(__('ticket.department'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('ticket.user'))
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
            ])
            ->filters([
                SelectFilter::make('user')
                    ->label(__('ticket.user'))
                    ->relationship('user', 'id')
                    ->indicateUsing(fn ($data) => $data['value'] ? __('ticket.user') . ': ' . User::find($data['value'])->name : null)
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
                SelectFilter::make('priority')
                    ->label(__('ticket.priority'))
                    ->options([
                        'low' => __('ticket.low'),
                        'medium' => __('ticket.medium'),
                        'high' => __('ticket.high'),
                    ]),
                SelectFilter::make('department')
                    ->label(__('ticket.department'))
                    ->options(array_combine(config('settings.ticket_departments'), config('settings.ticket_departments')), config('settings.ticket_departments')),
                SelectFilter::make('assigned_to')
                    ->label(__('ticket.assigned_to'))
                    ->relationship('user', 'id', fn (Builder $query) => $query->where('role_id', '!=', null))
                    ->indicateUsing(fn ($data) => $data['value'] ? __('ticket.assigned_to') . ': ' . User::find($data['value'])->name : null)
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getWidgets(): array
    {
        return [
            TicketsOverView::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'edit' => EditTicket::route('/{record}/edit'),
        ];
    }
}
