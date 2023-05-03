<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminLayout extends Component
{
    public $title;
    public $clients;

    public function __construct($title = '', $clients = false)
    {
        $this->title = $title;
        $this->clients = $clients ? true : false;
    }
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.admin');
    }
}
