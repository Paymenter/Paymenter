<?php

namespace Tests\Feature\Domains;

use App\Models\Tld;
use App\Models\TldPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DomainSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_formats_tld_prices_with_currency(): void
    {
        $tld = Tld::create(['tld' => 'com', 'enabled' => true]);

        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'register' => 12.00,
            'renew' => 13.00,
            'transfer' => 11.00,
        ]);

        $response = $this->get(route('domains.search'));

        $response->assertOk();
        $response->assertSee('$12.00 / year', false);
    }

    public function test_year_selector_only_lists_priced_terms(): void
    {
        $tld = Tld::create(['tld' => 'com', 'enabled' => true]);

        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 1,
            'register' => 12.00,
        ]);
        TldPrice::create([
            'tld_id' => $tld->id,
            'currency_code' => 'USD',
            'years' => 2,
            'register' => 22.00,
        ]);

        Livewire::test('domains.search')
            ->assertSet('availableYears', [1, 2])
            ->assertSee('2 years')
            ->assertDontSee('3 years');
    }

    public function test_available_domain_formats_term_total_and_per_year_price(): void
    {
        Livewire::test('domains.search')
            ->set('result', [
                'available' => true,
                'name' => 'example.com',
                'price' => 280.00,
                'years' => 2,
            ])
            ->assertSee('$280.00', false)
            ->assertSee('$140.00 / year', false);
    }
}
