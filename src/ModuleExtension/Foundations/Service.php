<?php

namespace ModuleExtension\Foundations;

use RuntimeException;
use ModuleExtension\Constraints\ServiceConstraint;
use ModuleExtension\Features\ModuleAccessFeature;
use ModuleExtension\Features\MethodAccessFeature;

abstract class Service implements ArrayAccess, ServiceConstraint
{
    use ModuleAccessFeature, MethodAccessFeature;

    public function offsetGet($offset)
    {
        throw new RuntimeException('');
    }

    public function offsetExists($offset)
    {
        $method = $this->resolveArrayMethod($offset);
        $method_name = "exists" . ($method->type == "Module" ? $method->type : $method->type . "Bind");
        return $this->$method_name($method->offset);
    }

    public function offsetSet($offset, $value) 
    {
        $method = $this->resolveArrayMethod($offset);
        $method_name = ($method->type == "Module" ? "set" : "bind") . $method->type;
        if (is_array($value)) {
            $this->$method_name($method->offset, $value[0], $value[1]);
        } else {
            $this->$method_name($method->offset, $value);
        }
    }

    public function offsetUnset($offset) 
    {
        $method = $this->resolveArrayMethod($offset);
        $method_name = ($method->type == "Module" ? "unset" : "unbind") . $method->type;
        $this->$method_name($method->offset);
    }

    protected function resolveArrayMethod($offset)
    {
        if (strpos($offset, '.') > 0) {
            $layers = explode($offset);
            switch (strtolower($layers[0])) {
                case "modules":
                    $method['type'] = 'Module';
                    break;
                case "methods":
                    $method['type'] = 'Method';
                    break;
                default:
                    throw new RuntimeException('');
            }
            $method['offset'] = $layers[1];
        } else {-
            $method = ['type' => 'Module', 'offset' => $offset];
        }
        return (object)($method);
    }

    public function __call($name, $arguments) 
    {
        if (isset($this->methods[$name])) {
            $callback = $this->methods[$name]['callback'];
            if (isset($this->methods[$name]['module'])) {
                $module = $this->methods[$name]['module'];
                if (is_array($module)) {
                    $module = array_merge(array_flip($module), $this->modules);
                } else {
                    $module = $this->modules[$module];
                }
                $arguments[] = $module;
            }
            return call_user_func_array($callback, $arguments);
        }
        throw new RuntimeException('');
    }
}
