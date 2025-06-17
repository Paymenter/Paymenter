<?php

namespace App\Livewire;

use App\Exceptions\DisplayException;

class Component extends \Livewire\Component
{
    use Traits\Disabled, Traits\HasNotifications;

    public function paginationView()
    {
        return 'components.pagination';
    }

    public function exception($e, $stopPropagation)
    {
        if ($e instanceof DisplayException) {
            $this->notify($e->getMessage(), 'error');
            $stopPropagation();
        }
    }
}
