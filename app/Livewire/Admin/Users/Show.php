<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;

class Show extends Component
{
    public User $user;
    
    public function render()
    {
        return view('admin.users.show')->layoutData(['title' => __('User: :name', ['name' => $this->user->name, 'id' => $this->user->id])]);
    }
}
