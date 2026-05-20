<?php

namespace App\Admin\Resources\TicketResource\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketsOverView extends BaseWidget
{
    protected ?string $pollingInterval = '20s';

    protected function getStats(): array
    {
        return [
            Stat::make(__('ticket.open_tickets'), Ticket::where('status', 'open')->count())
                ->color('success'),
            Stat::make(__('ticket.closed_tickets'), Ticket::where('status', 'closed')->count())
                ->color('danger'),
            Stat::make(__('ticket.replied_tickets'), Ticket::where('status', 'replied')->count())
                ->color('gray'),
        ];
    }
}
