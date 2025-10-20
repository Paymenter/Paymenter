<?php

namespace App\Livewire;

use App\Exceptions\DisplayException;
use App\Livewire\Traits\Disabled;
use App\Livewire\Traits\HasConfirmation;
use App\Livewire\Traits\HasNotifications;

class Component extends \Livewire\Component
{
    use Disabled, HasConfirmation, HasNotifications;

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
