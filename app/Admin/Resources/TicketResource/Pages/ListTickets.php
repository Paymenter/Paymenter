<?php

namespace App\Admin\Resources\TicketResource\Pages;

use App\Admin\Resources\TicketResource;
use App\Admin\Resources\TicketResource\Widgets\TicketsOverView;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TicketsOverView::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Tickets')
                ->icon('heroicon-m-user-group'),
            'open' => Tab::make('Open Tickets')
                ->icon('heroicon-o-lock-open')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'open')),
            'replied' => Tab::make('Replied Tickets')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'replied')),
            'closed' => Tab::make('Closed Tickets')
                ->icon('heroicon-o-lock-closed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'closed')),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'open';
    }
}
