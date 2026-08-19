<?php

namespace Tests\Feature;

use App\Admin\Resources\ConfigOptionResource\Pages\CreateConfigOption;
use App\Admin\Resources\ConfigOptionResource\Pages\EditConfigOption;
use App\Models\ConfigOption;
use App\Models\Plan;
use App\Models\Price;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminConfigOptionFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id,
        ]));
    }

    public function test_number_option_can_be_created_with_a_price_per_unit(): void
    {
        Livewire::test(CreateConfigOption::class)
            ->fillForm([
                'name' => 'Storage',
                'env_variable' => 'STORAGE',
                'type' => 'number',
                'min' => 100,
                'max' => 5000,
                'step' => 10,
                'show_as_slider' => true,
                'upgradable' => true,
                'plan' => [
                    [
                        'name' => 'Base',
                        'type' => 'recurring',
                        'billing_period' => 1,
                        'billing_unit' => 'month',
                        'pricing' => [
                            ['currency_code' => 'USD', 'price' => 2, 'setup_fee' => 0],
                        ],
                    ],
                ],
                'Options' => [
                    [
                        'name' => 'Price per GB',
                        'plan' => [
                            [
                                'name' => 'Per GB',
                                'type' => 'recurring',
                                'billing_period' => 1,
                                'billing_unit' => 'month',
                                'pricing' => [
                                    ['currency_code' => 'USD', 'price' => 0.05, 'setup_fee' => 0],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $option = ConfigOption::where('name', 'Storage')->first();

        $this->assertNotNull($option);
        $this->assertEquals([100.0, 5000.0, 10.0], [$option->min, $option->max, $option->step]);
        $this->assertCount(1, $option->children);
        $this->assertTrue($option->hasUnitPricing());
        $this->assertTrue($option->hasSlider());
        $this->assertTrue((bool) $option->upgradable);

        // 2 (base price) + 500 * 0.05 (price per unit)
        $this->assertEquals(27.0, $option->priceForQuantity(500, 1, 'month', 'USD')->price);
    }

    public function test_number_option_form_can_be_edited(): void
    {
        $option = ConfigOption::create([
            'name' => 'Storage',
            'env_variable' => 'STORAGE',
            'type' => 'number',
            'min' => 100,
            'max' => 5000,
            'step' => 10,
        ]);
        $child = ConfigOption::create([
            'name' => 'Price per GB',
            'type' => 'number',
            'parent_id' => $option->id,
        ]);
        $plan = Plan::factory()->create([
            'priceable_id' => $child->id,
            'priceable_type' => ConfigOption::class,
            'name' => 'Test Plan',
            'billing_unit' => 'month',
            'billing_period' => 1,
            'type' => 'recurring',
        ]);
        Price::factory()->create([
            'plan_id' => $plan->id,
            'price' => 0.05,
            'currency_code' => 'USD',
        ]);

        Livewire::test(EditConfigOption::class, ['record' => $option->getRouteKey()])
            ->assertFormSet([
                'type' => 'number',
                'min' => 100.0,
                'step' => 10.0,
                'show_as_slider' => false,
            ])
            ->fillForm(['max' => 10000])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals(10000.0, $option->refresh()->max);
    }
}
