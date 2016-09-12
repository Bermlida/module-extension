<?php

namespace Vista\Router\Traits;

trait RouteGetterTrait
{
    public function __get($name) 
    {
        return isset($this->$name) ?? $this->$name : null;
    }
}