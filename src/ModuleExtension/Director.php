<?php

namespace ModuleExtension;

use ReflectionMethod;

abstract class Director
{
    public function make(string $name)
    {
        if (isset($this->$name)) {
            return ($this->$name)->build();
        } else {
            throw new RuntimeException('');
        }
    }
}