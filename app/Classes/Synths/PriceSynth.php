<?php

namespace App\Classes\Synths;

use App\Classes\Price;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

/**
 * Class PriceSynth
 */
class PriceSynth extends Synth
{
    public static $key = 'price';

    public static function match($target)
    {
        return $target instanceof Price;
    }

    public static function dehydrate($instance)
    {
        return [[
            'price' => $instance->price,
            'currency' => $instance->currency,
            'setup_fee' => $instance->setup_fee,
            'has_setup_fee' => $instance->has_setup_fee,
            'is_free' => $instance->is_free,
            'dontShowUnavailablePrice' => $instance->dontShowUnavailablePrice,
            'formatted' => $instance->formatted,
        ], []];
    }

    public static function hydrate($instance)
    {
        return new Price(['price' => $instance['price'], 'setup_fee' => $instance['setup_fee'], 'currency' => $instance['currency']], $instance['is_free'], $instance['dontShowUnavailablePrice']);
    }
}
