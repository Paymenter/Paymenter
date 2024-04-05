<?php

namespace App\Livewire\Admin\Tickets;

use App\Models\Ticket;
use App\Traits\Tables\DesignTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Index extends DataTableComponent
{
    use DesignTrait;

    public string $status;

    public array $bulkActions = [
        'deleteTickets' => 'Delete Tickets',
        'closeTickets' => 'Close Tickets',
    ];

    public function deleteTickets(): void
    {
        $tickets = Ticket::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->delete();
        }

        $this->clearSelected();

    }

    public function closeTickets(): void
    {
        $tickets = Ticket::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->status = 'closed';
            $ticket->save();
        }

        $this->clearSelected();
    }


    public function builder(): Builder
    {
        if ($this->status == 'open') {
            return Ticket::query()
                ->where('status', '!=', 'closed');
        }
        return Ticket::query()
            ->where('status', 'closed');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.tickets.show', $row))
            ->setQueryStringDisabled()
            ->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
                if ($column->isField('status') && $row->status == 'open') {
                    return [
                        'default' => true,
                        'class' => '!text-green-500 !dark:text-green-400',
                    ];
                } else if ($column->isField('status') && $row->status == 'closed') {
                    return [
                        'default' => true,
                        'class' => '!text-red-500 !dark:text-red-400',
                    ];
                } else if ($column->isField('status') && $row->status == 'closed') {
                    return [
                        'default' => true,
                        'class' => '!text-red-500 !dark:text-red-400',
                    ];
                } else if ($column->isField('status') && $row->status == 'replied') {
                    return [
                        'default' => true,
                        'class' => '!text-blue-500 !dark:text-blue-400',
                    ];
                }
                return [
                    'default' => true,
                ];
            });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('Subject', 'title')
                ->sortable()->searchable(),
            Column::make('Priority', 'priority')
                ->sortable()->searchable(),
            Column::make('Client', 'user.first_name')
                ->sortable()->searchable(),
            Column::make('Status', 'status')
                ->sortable()->searchable(),
            Column::make('Created At', 'created_at')
                ->sortable()->searchable(),
        ];
    }
}
