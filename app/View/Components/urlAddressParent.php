<?php

namespace App\View\Components;

use Illuminate\View\Component;

class urlAddressParent extends Component
{
    public $text;
    public $fontAwesome;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($text, $fontAwesome)
    {
        $this->text = $text;
        $this->fontAwesome = $fontAwesome;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.admin.urlAddressParent');
    }
}
