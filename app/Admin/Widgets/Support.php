<?php

namespace App\Admin\Widgets;

use App\Admin\Resources\TicketResource;
use App\Models\Ticket;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Support extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    public function getHeading(): string
    {
        return __('dashboard.active_tickets');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->where('status', '!=', 'closed')
                    ->with('user')
                    ->withMax('messages', 'created_at')
                    ->orderByDesc('messages_max_created_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('subject')
                    ->label(__('dashboard.subject')),
                TextColumn::make('status')
                    ->label(__('dashboard.status'))
                    ->badge()
                    ->color(fn (Ticket $record) => match ($record->status) {
                        'open' => 'success',
                        'closed' => 'danger',
                        'replied' => 'warning',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('user.name')
                    ->label(__('dashboard.user')),
                TextColumn::make('created_at')
                    ->label(__('dashboard.created_at')),
            ])
            ->recordUrl(fn (Ticket $record) => TicketResource::getUrl('edit', ['record' => $record]))
            ->paginated(false);
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.support');
    }
}
