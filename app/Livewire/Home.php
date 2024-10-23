<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('home', [
            // Categories which have NO parent and at least one child
            'categories' => Category::whereNull('parent_id')->where(function ($query) {
                $query->whereHas('children')->orWhereHas('products');
            })->get(),
        ]);
    }
}
