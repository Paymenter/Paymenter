<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public $title;

    public $clients;

    public $description;

    public $image;

    public function __construct($title = '', $clients = false, $description = null, $image = null)
    {
        $this->title = $title;
        $this->clients = $clients ? true : false;
        $this->description = $description;
        $this->image = $image;
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return View
     */
    public function render()
    {
        return view('layouts.app');
    }
}
