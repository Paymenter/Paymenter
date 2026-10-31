<?php

namespace Tests\Feature;

use App\Classes\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class PriceTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_labels_are_english_by_default(): void
    {
        $this->assertSame('Free', (new Price(free: true))->formatted->price);
        $this->assertSame('Not available in your currency', (new Price(['price' => null, 'currency' => null]))->formatted->price);
    }

    public function test_price_labels_follow_the_locale(): void
    {
        Lang::addLines([
            'product.price_free' => 'Gratis',
            'product.unavailable_in_currency' => 'Niet beschikbaar in jouw valuta',
        ], 'nl');
        app()->setLocale('nl');

        $this->assertSame('Gratis', (new Price(free: true))->formatted->price);
        $this->assertSame('Niet beschikbaar in jouw valuta', (new Price(['price' => null, 'currency' => null]))->formatted->price);
    }
}
