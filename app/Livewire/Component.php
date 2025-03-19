<?php

namespace App\Livewire;

class Component extends \Livewire\Component
{
    use Traits\HasNotifications;

    public function paginationView()
    {
        return 'components.pagination';
    }
}
