<?php

namespace App\View\Components;

use Illuminate\View\Component;

class input extends Component
{
    public $type;
    public $key;
    public $placeholder;
    public $value;
    public $class;
    public $required;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($key, $placeholder = null, $type = 'text', 
                                $value = null, $class = null, $required = null)
    {
        $this->type = $type;
        $this->key = $key;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->class = $class;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.input');
    }
}
