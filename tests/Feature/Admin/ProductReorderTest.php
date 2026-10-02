<?php

namespace Tests\Feature\Admin;

use App\Admin\Resources\ProductResource\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductReorderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'role_id' => Role::where('name', 'admin')->value('id'),
        ]));
    }

    public function test_the_reorder_action_narrows_the_list_down_to_a_category(): void
    {
        $category = Category::factory()->create();

        $component = Livewire::test(ListProducts::class);

        $this->assertFalse($component->instance()->getTable()->isReorderable());

        $component->callAction('reorder', ['category' => $category->id])
            ->assertHasNoActionErrors();

        $this->assertSame($category->id, $component->get('tableFilters.category.value'));
        $this->assertTrue($component->instance()->getTable()->isReorderable());
        $this->assertTrue($component->instance()->isTableReordering());
    }

    public function test_the_reorder_action_requires_a_category(): void
    {
        Livewire::test(ListProducts::class)
            ->callAction('reorder', ['category' => null])
            ->assertHasActionErrors(['category' => 'required']);
    }

    public function test_reordering_requires_permission_to_update_products(): void
    {
        $role = Role::create([
            'name' => 'viewer',
            'permissions' => ['admin.products.viewAny', 'admin.products.view'],
        ]);

        $this->actingAs(User::factory()->create(['role_id' => $role->id]));

        $category = Category::factory()->create();
        $first = Product::factory()->create(['category_id' => $category->id]);
        $second = Product::factory()->create(['category_id' => $category->id]);

        Livewire::test(ListProducts::class)
            ->assertActionHidden('reorder')
            ->set('tableFilters.category.value', $category->id)
            ->call('reorderTable', [$second->getKey(), $first->getKey()]);

        $this->assertSame(
            [$first->getKey(), $second->getKey()],
            $category->products()->orderBy('sort')->pluck('id')->all()
        );
    }

    public function test_the_products_of_a_category_can_be_reordered(): void
    {
        $category = Category::factory()->create();

        $first = Product::factory()->create(['category_id' => $category->id]);
        $second = Product::factory()->create(['category_id' => $category->id]);
        $third = Product::factory()->create(['category_id' => $category->id]);

        Livewire::test(ListProducts::class)
            ->callAction('reorder', ['category' => $category->id])
            ->call('reorderTable', [$third->getKey(), $first->getKey(), $second->getKey()]);

        $this->assertSame(
            [$third->getKey(), $first->getKey(), $second->getKey()],
            $category->products()->orderBy('sort')->pluck('id')->all()
        );
    }

    public function test_reordering_leaves_the_other_categories_untouched(): void
    {
        $category = Category::factory()->create();
        $other = Category::factory()->create();

        $first = Product::factory()->create(['category_id' => $category->id]);
        $second = Product::factory()->create(['category_id' => $category->id]);
        $third = Product::factory()->create(['category_id' => $other->id, 'sort' => 40]);
        $fourth = Product::factory()->create(['category_id' => $other->id, 'sort' => 50]);

        Livewire::test(ListProducts::class)
            ->callAction('reorder', ['category' => $category->id])
            ->call('reorderTable', [$second->getKey(), $first->getKey()]);

        $this->assertSame(
            [$second->getKey(), $first->getKey()],
            $category->products()->orderBy('sort')->pluck('id')->all()
        );

        $this->assertSame(40, $third->refresh()->sort);
        $this->assertSame(50, $fourth->refresh()->sort);
    }
}
