<?php

namespace App\View\Components;

use Illuminate\View\Component;

class header extends Component
{
    public $pageName;
    public $buttonValue;
    public $type;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($pageName, $buttonValue = null, $type = 0)
    {
        $this->pageName = $pageName;
        $this->buttonValue = $buttonValue;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.header');
    }
}
