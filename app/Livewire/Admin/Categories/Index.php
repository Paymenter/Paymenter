<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Traits\Tables\DesignTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Index extends DataTableComponent
{
    use DesignTrait;


    public array $bulkActions = [
        'deleteCategories' => 'Delete Categories',
    ];

    public function builder(): Builder
    {
        // Also select the order 
        return Category::query()->with('parent');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.categories.edit', $row));
        $this->setReorderStatus(true);
        $this->setDefaultReorderSort('order');
        $this->setDefaultSort('order', 'asc');
        $this->setAdditionalSelects('categories.order');
    }

    public function deleteCategories(): void
    {
        $products = Category::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($products as $product) {
            Product::where('category_id', $product->id)->delete();
            $product->delete();
        }

        $this->clearSelected();
    }

    public function reorder(array $items): void
    {
        foreach ($items as $item) {
            Category::find($item[$this->getPrimaryKey()])->update(['order' => (int)$item[$this->getDefaultReorderColumn()]]);
        }
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable(),
            Column::make('Name', 'name')
                ->sortable()->searchable(),
            Column::make('Slug', 'slug')
                ->sortable()->searchable(), 
            Column::make('Parent', 'parent.name')
                ->sortable()->searchable(),
            Column::make('Created At', 'created_at')
                ->sortable()->searchable(),

        ];
    }
}
