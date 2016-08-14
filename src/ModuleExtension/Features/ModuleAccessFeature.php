<?php

namespace ModuleExtension\Features;

use ReflectionClass;
use RuntimeException;

trait ModuleAccessFeature
{
    protected $modules = [];

    public function existsModule(string $name)
    {
        return isset($this->modules[$name]);
    }

    public function setModule(string $name, $module, array $construct_params = [])
    {
        if (is_string($module) || is_object($module)) {
            if (is_string($module)) {
                if (count($construct_params) > 0) {
                    $class = new ReflectionClass($module);
                    $module = $class->newInstanceArgs($construct_params);
                } else {
                    $module = new $module;
                }
            }
            $this->modules[$name] = $module;
        } else {
            throw new RuntimeException('');
        }
    }

    public function unsetModule(string $name)
    {
        if (isset($this->modules[$name])) {
            unset($this->modules[$name]);
        }
    }
}