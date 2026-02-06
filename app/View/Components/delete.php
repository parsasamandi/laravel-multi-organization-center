<?php

namespace App\View\Components;

use Illuminate\View\Component;

class delete extends Component
{
    public $title;
    public $isEnglish;
    
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $isEnglish)
    {
        $this->title = $title;
        $this->isEnglish = $isEnglish;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.admin.delete');
    }
}
