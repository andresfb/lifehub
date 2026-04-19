<?php

namespace App\View\Components;

use Illuminate\View\View;

class Input extends Control
{
    public function __construct($name, $id = null, $value = '', $label = '', $description = '', $bag = 'default', $type = 'text')
    {
        parent::__construct($name, $id, $value, $label, $description, $bag);
        $this->controlAttributes = $this->controlAttributes->merge(['type' => $type]);
    }

    public function render(): View
    {
        return view('components.input');
    }
}
