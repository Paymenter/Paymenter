<?php

namespace App\Models\Traits;

trait Priceable
{
    protected static function bootPriceable()
    {
        self::deleting(function ($model) {
            if ($model->prices) {
                foreach ($model->prices as $price) {
                    $price->delete();
                }
            } elseif ($model->price) {
                $model->price()->delete();
            }
        });
    }
}
