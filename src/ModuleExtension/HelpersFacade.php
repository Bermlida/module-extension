<?php

namespace App\Models\Foundations;

use ReflectionClass;
use RuntimeException;

abstract class HelpersFacade
{
    private $storage;

    public function __construct($storage = null, array $processor = [])
    {
        $this->storage = $storage;
        foreach ($processor as $function => $arguments) {
            $this->__call($function, (array)$arguments);
        }
    }

    protected static function resolveHelperName($name)
    {
        $facade_name = (new ReflectionClass(new static))->getName();
        $namespace = str_replace(["Facades", "Facade"], ["Helpers", ""], $facade_name);
        return $namespace . '\\' . $name;
    }

    protected static function failedCall($name, $arguments)
    {
        $params = print_r($arguments, true);
        return new RuntimeException('Helper function call failed. function name: ' . $name . ', params: ' . $params);
    }

    public function __get($name) 
    {
        if ($name == "result") {
            return $this->storage;
        }
        return null;
    }

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

    public static function __callStatic($name, $arguments) 
    {
        $name = static::resolveHelperName($name);
        if (function_exists($name)) {
            return call_user_func_array($name, $arguments);
        }
        throw static::failedCall($name, $arguments);
    }
}
