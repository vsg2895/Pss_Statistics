<?php

namespace App\View\Components\Datepicker;

use Illuminate\View\Component;

class DateRange extends Component
{
    /**
     * @var string
     */
    public string $buttonTitle;

    /**
     * DateRange constructor.
     * @param string $buttonTitle
     */
    public function __construct(string $buttonTitle = "")
    {
        $this->buttonTitle = $buttonTitle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.datepicker.date-range');
    }
}
