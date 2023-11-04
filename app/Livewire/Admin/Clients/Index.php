<?php

namespace App\Livewire\Admin\Clients;

use App\Models\User;
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
        return User::query();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.clients.edit', $row));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('User', 'first_name')
                ->sortable()->searchable(),
            Column::make('Email', 'email')
                ->sortable()->searchable(),
            Column::make('Created At', 'created_at')
                ->sortable(),
        ];
    }
}
