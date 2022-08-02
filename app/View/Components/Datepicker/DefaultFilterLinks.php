<?php

namespace App\View\Components\Datepicker;

use Illuminate\View\Component;
use function view;

class DefaultFilterLinks extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $routeName;
    public $routeParam;

    public $object;

    public function __construct($routeName, $routeParam, $object)
    {
        $this->routeName = $routeName;
        $this->routeParam = $routeParam;
        $this->object = $object;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.datepicker.default-filter-links');
    }
}
