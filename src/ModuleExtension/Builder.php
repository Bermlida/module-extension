<?php

namespace ModuleExtension;

use Closure;
use ReflectionClass;
use RuntimeException;

abstract class Builder
{
    protected $object;

    abstract public function build();

    public function __construct(string $class)
    {
        $this->object = $class;
    }
/*
    public function bind(string $name, Closure $closure)
    {
        $this->bindings[$name] = $closure;
    }

    public function make(string $name, array $arguments = [])
    {
        if (isset($this->bindings[$name])) {
            array_unshift($arguments, $this);
            return $this->callClosure($this->bindings[$name], $arguments);
        } else {
            throw new RuntimeException('');
        }
    }
*/
    public function construct(array $arguments = [], $closure = null)
    {
        if (!is_callable($closure)) {
            $object = $this->object;
            if (count($arguments > 0)) {
                $object = (new ReflectionClass($object))->newInstanceArgs($arguments);
            } else {
                $object  = new $object(); 
            }
        } else {
            $object = $this->callClosure($closure, $arguments);
        }
        $this->object = $object;
    }

    public function setProperty(string $name, $value, array $params = [])
    {        
        $class = new ReflectionClass($this->object);
        if ($class->hasProperty($name)) {
            $value = is_callable($value) ? $this->callClosure($value, $params) : $value;
            
            $property = $class->getProperty($name);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            $property->setValue($this->object, $value);
        } else {
            throw new RuntimeException('');
        }
        return $this;
    }

    protected function callClosure(Closure $closure, $arguments)
    {
        switch (count($arguments)) {
            case 0:
                return $closure();
            case 1: 
                return $closure($arguments[0]);
            case 2: 
                return $closure($arguments[0], $arguments[1]);
            case 3: 
                return $closure($arguments[0], $arguments[1], $arguments[2]);
            case 4:
                return $closure($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            case 5:
                return $closure($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
            case 6:
                return $closure($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
            default:
                return call_user_func_array($closure, $arguments); 
        }
    }
}