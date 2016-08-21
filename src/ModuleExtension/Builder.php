<?php

namespace ModuleExtension;

use ReflectionClass;
//use ReflectionMethod;
use RuntimeException;

abstract class Builder
{
    protected $object;

    abstract public function build();

    public function __construct(array $arguments = [])
    {
        if (!method_exists($this, 'construct')) {
            $class = new ReflectionClass($this->object);
            $object = $class->newInstanceArgs($arguments);
        } else {
            switch (count($arguments)) {
                case 0: $object = $this->construct(); break;
                case 1: $object = $this->construct($arguments[0]); break;
                case 2: $object = $this->construct($arguments[0], $arguments[1]); break;
                case 3: $object = $this->construct($arguments[0], $arguments[1], $arguments[2]); break;
                case 4: $object = $this->construct($arguments[0], $arguments[1], $arguments[2], $arguments[3]); break;
                default: $object = call_user_func_array([$this, 'construct'], $arguments); break;
            }
        }
        $this->object = $object;
    }

    protected function setProperty(string $name, $value, $class = null)
    {        
        $class = $class ?? new ReflectionClass($this->object);
        if ($class->hasProperty($name)) {
            $property = $class->getProperty($name);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            $property->setValue($this->object, $value);
        } else {
            throw new RuntimeException('');
        }
    }

    public function __call($name, $arguments)
    {
        $class = new ReflectionClass($this->object);
        if ($class->hasProperty($name)) {
            if (isset($arguments[1]) && is_callable($arguments[1])) {
                $value = call_user_func($arguments[1], $arguments[0]);
            } else {
                $value = $arguments[0];
            }
            $this->setProperty($name, $value, $class);
        } else {
            throw new RuntimeException('');
        }
        return $this;
    }
}