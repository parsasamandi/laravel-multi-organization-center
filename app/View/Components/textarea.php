<?php

namespace App\View\Components;

use Illuminate\View\Component;

class textarea extends Component
{
    public $key; // Id, name
    public $placeholder; // Label, placeholder
    public $value; // Value | default: null
    public $rows; // Rows | default: 3
    public $class; // Class
    public $readonly; // Readonly
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($key, $placeholder, 
                                $rows = 3, $value = null, $class = null, $readonly = null)
    {
        $this->key = $key;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->rows = $rows;
        $this->class = $class;
        $this->readonly = $readonly;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.textarea');
    }
}
