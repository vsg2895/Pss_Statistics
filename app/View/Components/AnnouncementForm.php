<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AnnouncementForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $routeName;
    public $routeParam;
    public $text;
    public $object;

    public function __construct($routeName, $routeParam, $text, $object)
    {
        $this->routeName = $routeName;
        $this->routeParam = $routeParam;
        $this->text = $text;
        $this->object = $object;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.announcement-form');
    }
}
