<?php

namespace Tests\Feature;

use App\Models\ConfigOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DynamicSliderAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_dynamic_slider_renders_aria_attributes_and_live_regions(): void
    {
        $fixture = $this->createProduct();

        $option = ConfigOption::create([
            'name' => 'Memory',
            'env_variable' => 'MEMORY',
            'type' => 'dynamic_slider',
            'sort' => 1,
            'hidden' => false,
            'upgradable' => false,
            'metadata' => [
                'min' => 1,
                'max' => 64,
                'step' => 1,
                'default' => 8,
                'unit' => 'GB',
                'display_unit' => 'GB',
                'display_divisor' => 1,
                'resource_type' => 'memory',
                'pricing' => [
                    'model' => 'linear',
                    'base_price' => 0,
                    'rate_per_unit' => 2,
                ],
            ],
        ]);

        DB::table('config_option_products')->insert([
            'config_option_id' => $option->id,
            'product_id' => $fixture->product->id,
        ]);

        $html = view('components.form.configoption', [
            'config' => $option->fresh(),
            'name' => "configOptions.{$option->id}",
            'plan' => $fixture->plan,
            'showPriceTag' => true,
        ])->render();

        $this->assertStringContainsString('role="slider"', $html);
        $this->assertStringContainsString('aria-valuemin="1"', $html);
        $this->assertStringContainsString('aria-valuemax="64"', $html);
        $this->assertStringContainsString(':aria-valuenow="value"', $html);
        $this->assertStringContainsString(':aria-valuetext="formattedValue"', $html);
        $this->assertStringContainsString('aria-labelledby="slider-label-'.$option->id.'"', $html);
        $this->assertStringContainsString('aria-describedby="slider-price-'.$option->id.' slider-hint-'.$option->id.'"', $html);
        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('aria-live="polite"', $html);
        $this->assertStringContainsString('class="sr-only"', $html);

        // Also verify Obsidian theme has identical a11y attributes
        $obsidianHtml = view()->file(base_path('themes/obsidian/views/components/form/configoption.blade.php'), [
            'config' => $option->fresh(),
            'name' => "configOptions.{$option->id}",
            'plan' => $fixture->plan,
            'showPriceTag' => true,
        ])->render();

        $this->assertStringContainsString('role="slider"', $obsidianHtml);
        $this->assertStringContainsString('aria-valuemin="1"', $obsidianHtml);
        $this->assertStringContainsString('aria-valuemax="64"', $obsidianHtml);
        $this->assertStringContainsString('role="status"', $obsidianHtml);
        $this->assertStringContainsString('aria-live="polite"', $obsidianHtml);
        $this->assertStringContainsString('aria-describedby="slider-price-'.$option->id.' slider-hint-'.$option->id.'"', $obsidianHtml);
    }

    public function test_dynamic_slider_renders_focus_visible_focus_ring_classes(): void
    {
        $fixture = $this->createProduct();

        $option = ConfigOption::create([
            'name' => 'Memory',
            'env_variable' => 'MEMORY',
            'type' => 'dynamic_slider',
            'sort' => 1,
            'hidden' => false,
            'upgradable' => false,
            'metadata' => [
                'min' => 1,
                'max' => 64,
                'step' => 1,
                'default' => 8,
                'unit' => 'GB',
                'display_unit' => 'GB',
                'display_divisor' => 1,
                'resource_type' => 'memory',
                'pricing' => [
                    'model' => 'linear',
                    'base_price' => 0,
                    'rate_per_unit' => 2,
                ],
            ],
        ]);

        DB::table('config_option_products')->insert([
            'config_option_id' => $option->id,
            'product_id' => $fixture->product->id,
        ]);

        $html = view('components.form.configoption', [
            'config' => $option->fresh(),
            'name' => "configOptions.{$option->id}",
            'plan' => $fixture->plan,
            'showPriceTag' => true,
        ])->render();

        $this->assertStringContainsString('focus-visible:ring-2', $html);

        // Also verify Obsidian theme has the focus ring class
        $obsidianHtml = view()->file(base_path('themes/obsidian/views/components/form/configoption.blade.php'), [
            'config' => $option->fresh(),
            'name' => "configOptions.{$option->id}",
            'plan' => $fixture->plan,
            'showPriceTag' => true,
        ])->render();

        $this->assertStringContainsString('focus-visible:ring-2', $obsidianHtml);
    }
}
