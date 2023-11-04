<?php

namespace App\Livewire\Admin\ConfigurableOptions;

use App\Helpers\ExtensionHelper;
use App\Models\ConfigurableGroup;
use App\Models\OrderProductConfig;
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
        return ConfigurableGroup::query();
    }

    public array $bulkActions = [
        'deleteConfigurableGroups' => 'Delete Configurable Groups',
    ];

    public function deleteConfigurableGroups(): void
    {
        $configurableGroups = ConfigurableGroup::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($configurableGroups as $configurableGroup) {
            $configurableGroup->configurableOptions->each(function ($configurableOption) {
                OrderProductConfig::query()->where('is_configurable_option', $configurableOption->id)->each(function ($orderProductConfig) {
                    $orderProductConfig->is_configurable_option = true;
                });
                $configurableOption->delete();
            });
            $configurableGroup->delete();
        }

        $this->clearSelected();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.configurable-options.edit', $row));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('Name', 'name')
                ->sortable()->searchable(),
            Column::make('Description', 'description')
                ->sortable()->searchable(),
            Column::make('Created At', 'created_at')
                ->sortable()->searchable(),
        ];
    }
}
