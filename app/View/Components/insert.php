<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Insert extends Component
{
    public $size;
    public $formId;
    public $english;

    /**
     * Create a new component instance.
     *
     * @param string $size
     * @param string $formId
     * @param bool $english
     * @return void
     */
    public function __construct($size, $formId, $english = null)
    {
        $this->size = $size;
        $this->formId = $formId;
        $this->english = $english;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.admin.insert');
    }
}
