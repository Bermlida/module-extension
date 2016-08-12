<?php

namespace ModuleExtension\Foundations;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

abstract class Service
{

    protected $modules = [];

    protected $methods = [];

    protected function method($modules)
    {
        return [];
    }

    public function addModule(string $name, $module)
    {
        //$facade_name = (new ReflectionClass(new static))->getName();
        //return str_replace(["Facades", "Facade"], ["Helpers", ""], $facade_name);
        //$this->modules = array_merge($this->modules, $modules);
        $this->modules[$name] = $module;
    }

    public function removeModule(string $name)
    {
        if (isset($this->modules[$name])) {
            unset($this->modules[$name]);
        }
        //$error_message = 'Helper function call failed. function name: ' . $name;
        //$error_message .= ', params: ' . print_r($arguments, true);
        //return new RuntimeException($error_message);
    }

    protected function addMethod($name, $method) 
    {
        //$this->$method_name = $callback;
        //if ($name == "result") {
        //    return $this->storage;
        //}
        //return $this->modules;
    }

    public function __call($name, $arguments) 
    {
        $available_methods = $this->methods($this->modules);
        if (isset($available_methods[$name])) {
            return call_user_func_array($available_methods[$name], $arguments);
        }
        throw static::failedCall($helper_name, $arguments);
    }
/*
    public function __call($name, $arguments) 
    {
        $name = $this->resolveHelperName($name);
        if (function_exists($name)) {
            if (!is_null($this->storage)) {
                array_unshift($arguments, $this->storage);
            }
            
            $return = call_user_func_array($name, $arguments);
            $this->storage = $return ?? $this->storage;
            return $this;
        }
        throw static::failedCall($name, $arguments);
    }
*/
}
