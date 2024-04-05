<?php

namespace App\Livewire\Admin\Products;

use App\Helpers\ExtensionHelper;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Traits\Tables\DesignTrait;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ComponentColumn;

class Index extends DataTableComponent
{
    use DesignTrait;

    public Category $category;


    public array $bulkActions = [
        'deleteProducts' => 'Delete Products',
    ];

    public function builder(): Builder
    {
        return Product::query()
            ->where('category_id', $this->category->id);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')->setTableRowUrl(fn ($row) => route('admin.products.edit', $row));
        $this->setReorderStatus(true);
        $this->setDefaultReorderSort('order');
        $this->setQueryStringDisabled();
        $this->setDefaultSort('order', 'asc');
    }

    public function deleteProducts(): void
    {
        $products = Product::query()
            ->whereIn('id', $this->getSelected())
            ->get();

        foreach ($products as $product) {
            OrderProduct::where('product_id', $product->id)->delete();
            $product->prices->delete();
            $product->delete();
        }


        $this->clearSelected();
    }

    public function reorder(array $items): void
    {
        foreach ($items as $item) {
            Product::find($item[$this->getPrimaryKey()])->update(['order' => (int)$item[$this->getDefaultReorderColumn()]]);
        }
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
