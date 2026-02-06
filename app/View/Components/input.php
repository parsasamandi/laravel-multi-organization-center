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
    public $readonly;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($key = null, $placeholder = null, $type = 'text', 
                                $value = null, $class = null, $required = false, $readonly = false)
    {
        $this->type = $type;
        $this->key = $key;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->class = $class;
        $this->required = $required;
        $this->readonly = $readonly;
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
