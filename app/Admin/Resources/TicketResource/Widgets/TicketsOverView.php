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
            Stat::make('Open Tickets', Ticket::where('status', 'open')->count())
                ->color('success'),
            Stat::make('Closed Tickets', Ticket::where('status', 'closed')->count())
                ->color('danger'),
            Stat::make('Replied Tickets', Ticket::where('status', 'replied')->count())
                ->color('gray'),
        ];
    }
}
