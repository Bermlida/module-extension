<?php

namespace ModuleExtension\Features;

use RuntimeException;

trait MethodAccessFeature
{
    protected $methods = [];

    public function existsMethodBind(string $name)
    {
        return isset($this->methods[$name]);
    }

    public function bindMethod(string $name, Closure $callback, $module = null)
    {
        $this->methods[$name]['callback'] = $callback;
        if (!is_null($module)) {
            if (is_string($module) || is_array($module)) {
                $this->methods[$name]['module'] = $module;
            } else {
                throw new RuntimeException('');
            }
        }
    }

    public function unbindMethod(string $name)
    {
        if (isset($this->methods[$name])) {
            unset($this->methods[$name]);
        }
    }
}
