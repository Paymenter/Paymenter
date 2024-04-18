<?php
 
namespace App\Livewire\Admin\Users;
 
use App\Models\User;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
 
class Table extends DataTableComponent
{
    protected $model = User::class;
 
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTableRowUrl(function($row) {
            return route('admin.users.show', $row);
        });
    }
 
    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make('First Name', 'first_name')
                ->sortable(),
        ];
    }
}