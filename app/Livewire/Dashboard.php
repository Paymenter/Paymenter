<?php

namespace App\Livewire;

class Dashboard extends Component
{
    public $activeComponent = 'services';

    public function render()
    {
        return view('dashboard')->layoutData([
            'sidebar' => true,
        ]);
    }
}
