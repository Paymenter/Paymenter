<?php

namespace App\Traits\Tables;

use Livewire\Attributes\Url;
use Illuminate\Support\Facades\App;
use Rappasoft\LaravelLivewireTables\Views\Column;

trait DesignTrait
{

    public function bootDesignTrait()
    {
        $this->setTrAttributes(function (
            $row,
            $index
        ) {
            if ($index % 2 === 0) {
                return [
                    'class' => 'bg-secondary-50 dark:bg-secondary-200 dark:text-darkmodetext',
                ];
            }

            return [
                'default' => true,
                'class' => 'bg-secondary-50 dark:bg-secondary-100 dark:text-darkmodetext',
            ];
        });

        $this->setTableWrapperAttributes([
            'class' => '!overflow-y-auto',
        ]);

        $this->setTheadAttributes([
            'class' => '!bg-transparent',
        ]);

        Column::make('money')
            ->format(function ($value) {
                if (config('settings::currency_position') == 'left') {
                    return config('settings::currency_sign') . number_format($amount, 2);
                }
                if (config('settings::currency_position') == 'right') {
                    return number_format($amount, 2) . config('settings::currency_sign');
                }
            });
    }
}
