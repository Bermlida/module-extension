<?php

namespace ModuleExtension\Foundations;


use ModuleExtension\Constraints\ServiceConstraint;
use ModuleExtension\Features\ModuleAccessFeature;
use ModuleExtension\Features\MethodAccessFeature;
use RuntimeException;

abstract class Service implements ArrayAccess, ServiceConstraint
{
    use ModuleAccessFeature, MethodAccessFeature;

    public function offsetGet($offset)
    {
        throw new RuntimeException('');
    }

    public function offsetExists($offset)
    {
        return $this->existsModule($offset);
    }

    public function offsetSet($offset, $value) 
    {
        $this->setModule($offset, $value);
    }

    public function offsetUnset($offset) 
    {
        $this->unsetModule($offset);
    }

    public function __call($name, $arguments) 
    {
        if (isset($this->methods[$name])) {
            $callback = $this->methods[$name]['callback'];
            if (isset($this->methods[$name]['module'])) {
                $use_module = $this->methods[$name]['module'];
                if (is_array($use_module)) {
                    $use_module = array_merge(array_flip($use_module), $this->modules);
                } else {
                    $use_module = $this->modules[$use_module];
                }
                $arguments[] = $use_module;
            }
            return call_user_func_array($callback, $arguments);
        }
        throw new RuntimeException('');
    }
}
