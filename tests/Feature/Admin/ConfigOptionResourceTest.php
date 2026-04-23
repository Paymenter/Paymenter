<?php

namespace Tests\Feature\Admin;

use App\Admin\Resources\ConfigOptionResource\Pages\CreateConfigOption;
use App\Admin\Resources\ConfigOptionResource\Pages\EditConfigOption;
use App\Models\ConfigOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConfigOptionResourceTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $this->actingAs($admin);

        return $admin;
    }

    private function baseDynamicSliderFormData(array $metadataOverrides = []): array
    {
        return [
            'name' => 'Memory',
            'env_variable' => 'MEMORY',
            'type' => 'dynamic_slider',
            'metadata' => array_merge([
                'resource_type' => 'memory',
                'min' => 1024,
                'max' => 65536,
                'step' => 1024,
                'default' => 4096,
                'unit' => 'MB',
                'display_unit' => 'GB',
                'display_divisor' => 1024,
                'pricing' => [
                    'model' => 'linear',
                    'base_price' => 0,
                    'rate_per_unit' => 2.00,
                ],
            ], $metadataOverrides),
        ];
    }

    public function test_create_accepts_valid_dynamic_slider_pricing(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateConfigOption::class)
            ->fillForm($this->baseDynamicSliderFormData())
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('config_options', [
            'name' => 'Memory',
            'type' => 'dynamic_slider',
        ]);
    }

    public function test_create_rejects_unknown_pricing_model(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateConfigOption::class)
            ->fillForm($this->baseDynamicSliderFormData([
                'pricing' => [
                    'model' => 'base_plus_addon',
                    'rate_per_unit' => 2.00,
                ],
            ]))
            ->call('create');

        // Record must not have been persisted
        $this->assertDatabaseMissing('config_options', [
            'name' => 'Memory',
            'type' => 'dynamic_slider',
        ]);
    }

    public function test_create_rejects_out_of_order_tier_up_to(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateConfigOption::class)
            ->fillForm($this->baseDynamicSliderFormData([
                'pricing' => [
                    'model' => 'tiered',
                    'tiers' => [
                        ['up_to' => 16, 'rate' => 2.5],
                        ['up_to' => 4,  'rate' => 3.0],
                    ],
                ],
            ]))
            ->call('create');

        $this->assertDatabaseMissing('config_options', [
            'type' => 'dynamic_slider',
        ]);
    }

    public function test_create_rejects_negative_rate(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateConfigOption::class)
            ->fillForm($this->baseDynamicSliderFormData([
                'pricing' => [
                    'model' => 'linear',
                    'rate_per_unit' => -1.0,
                ],
            ]))
            ->call('create');

        $this->assertDatabaseMissing('config_options', [
            'type' => 'dynamic_slider',
        ]);
    }

    public function test_create_rejects_negative_overage_rate_for_base_addon(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateConfigOption::class)
            ->fillForm($this->baseDynamicSliderFormData([
                'pricing' => [
                    'model' => 'base_addon',
                    'included_units' => 4,
                    'overage_rate' => -1.0,
                ],
            ]))
            ->call('create');

        $this->assertDatabaseMissing('config_options', [
            'type' => 'dynamic_slider',
        ]);
    }

    public function test_edit_rejects_invalid_pricing(): void
    {
        $this->actingAsAdmin();

        $option = ConfigOption::create([
            'name' => 'Memory',
            'env_variable' => 'MEMORY',
            'type' => 'dynamic_slider',
            'hidden' => false,
            'upgradable' => false,
            'metadata' => [
                'resource_type' => 'memory',
                'min' => 1024,
                'max' => 65536,
                'step' => 1024,
                'default' => 4096,
                'unit' => 'MB',
                'display_unit' => 'GB',
                'display_divisor' => 1024,
                'pricing' => [
                    'model' => 'linear',
                    'base_price' => 0,
                    'rate_per_unit' => 2.00,
                ],
            ],
        ]);

        Livewire::test(EditConfigOption::class, ['record' => $option->getRouteKey()])
            ->fillForm($this->baseDynamicSliderFormData([
                'pricing' => [
                    'model' => 'unknown_model',
                    'rate_per_unit' => 1.0,
                ],
            ]))
            ->call('save');

        // The record should still exist with its original pricing intact
        $option->refresh();
        $this->assertSame('linear', $option->metadata['pricing']['model']);
    }

    public function test_non_dynamic_slider_types_bypass_pricing_validation(): void
    {
        $this->actingAsAdmin();

        // A text config option never has pricing metadata; form should pass even if
        // metadata.pricing is missing/invalid-looking.
        Livewire::test(CreateConfigOption::class)
            ->fillForm([
                'name' => 'Hostname',
                'env_variable' => 'HOSTNAME',
                'type' => 'text',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('config_options', [
            'name' => 'Hostname',
            'type' => 'text',
        ]);
    }
    // -----------------------------------------------------------------------
    // Patch 4: upgradable toggle hidden for dynamic_slider
    // -----------------------------------------------------------------------

    public function test_upgradable_toggle_not_rendered_for_dynamic_slider(): void
    {
        $this->actingAsAdmin();

        // The upgradable checkbox should not be visible when type=dynamic_slider.
        // We verify this by creating a dynamic_slider option and confirming
        // the upgradable field is not set (defaults to false) and the form
        // does not expose it as a settable field.
        Livewire::test(CreateConfigOption::class)
            ->fillForm($this->baseDynamicSliderFormData())
            ->assertFormFieldIsHidden('upgradable');
    }

    public function test_upgradable_toggle_visible_for_select_type(): void
    {
        $this->actingAsAdmin();

        Livewire::test(CreateConfigOption::class)
            ->fillForm([
                'name' => 'OS',
                'env_variable' => 'OS',
                'type' => 'select',
            ])
            ->assertFormFieldIsVisible('upgradable');
    }
}
