<?php

namespace App\Livewire\Auth;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Logout extends Component
{
    public function logout()
    {
        Auth::logout();

        return redirect('/');
    }

    public function render()
    {
        return view('auth.logout');
    }
}
