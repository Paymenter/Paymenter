<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SidebarNavigationItem extends Component
{
    /**
     * The route which the user gets directed to.
     *
     * @var string
     */
    public $route;

    /**
     * Bootstrap icon.
     *
     * @var string
     */
    public $icon;

    /**
     * Item is a dropdown item.
     *
     * @var bool
     */
    public $dropdown;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $route, string $icon, bool $dropdown = false)
    {
        $this->route = $route;
        $this->icon = $icon;
        $this->dropdown = $dropdown;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.sidebar-navigation-item');
    }
}
