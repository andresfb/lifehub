<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HigherOrderWhenProxy;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;

abstract class Control extends Component
{
    public mixed $id;

    public string|array|null $value;

    public mixed $label;

    public mixed $description;

    public HigherOrderWhenProxy|ComponentAttributeBag $controlAttributes;

    public function __construct($name, $id = null, $value = '', $label = '', $description = '', $bag = 'default')
    {
        $sessionPath = self::sessionPath($name);
        $this->value = old($sessionPath, $value);
        $this->label = $label;
        $this->description = $description;
        $this->id = $id ?? $name;
        $this->controlAttributes = $this->newAttributeBag([
            'name' => $name,
            'id' => $this->id,
        ])->when($this->errorBag($bag)->has($sessionPath), function ($attributes) {
            $attributes->offsetSet('aria-invalid', 'true');
            $attributes->offsetSet('aria-describedby', $attributes->prepends($this->id.'_error'));
        })->when($this->description, function ($attributes) {
            $attributes->offsetSet('aria-describedby', $attributes->prepends($this->id.'_description'));
        });
    }

    protected function errorBag($name = 'default')
    {
        $bags = View::shared('errors', static fn () => Session::get('errors', new ViewErrorBag));

        return $bags->getBag($name);
    }

    public static function sessionPath($name): string
    {
        return trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    }
}
