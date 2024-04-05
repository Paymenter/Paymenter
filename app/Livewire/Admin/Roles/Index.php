<?php

namespace App\Livewire\Admin\Roles;

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\Role;
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
        return Role::query();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.roles.edit', $row));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('Name', 'name')
                ->sortable()->searchable(),
            Column::make('Created At', 'created_at')
                ->sortable()->searchable(),
        ];
    }
}
