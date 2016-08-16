<?php

namespace ModuleExtension\Foundations;

use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

abstract class Repository
{
    protected $entity_proxy = [];
/*
    public function __construct()
    {
        $class = new ReflectionClass($this);
        $properties = $class->getProperties(ReflectionProperty::IS_PRIVATE);
        
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $property_name = $property->getName();
            $property_value = $property->getValue($this);

            if (!is_string($property_value)) {
                $segments = explode("_", $property_name);
                if (count($segments) > 1) {
                    $callback = function ($name) {
                        return ucfirst(strtolower($name));
                    };
                }  else {
                    $callback = "ucfirst";
                }
                $property_value = implode(array_map($callback, $segments));
            }

            $entity_constraint = "ModuleExtension\\Constraints\\EntityConstraint";
            $namespace = (strpos($property_value, "\\") === false) ? $class->getNamespaceName() . "\\Entities\\" : "";
            $property_value = $namespace . $property_value;
            if ((new ReflectionClass($property_value))->implementsInterface($entity_constraint)) {
                $property->setValue($this, new $property_value);
            }
        }
    }
*/
    public function entity($entity, $entity_proxy = '')
    {
        if (!isset($this->entity_proxy[$entity])) {
            $class = new ReflectionClass($this);
            
            if ($class->hasProperty($name)) {
                $property = $class->getProperty($name);
                if ($property->isPrivate() || $property->isProtected()) {
                    $property->setAccessible(true);
                }
                $property_value = $property->getValue($this);

                if (is_object($property_value)) {
                    $entity_constraint = "ModuleExtension\\Constraints\\EntityConstraint";
                    if ((new ReflectionClass($property_value))->implementsInterface($entity_constraint)) {
                        if (empty($entity_proxy)) {
                            $this->entity_proxy[$entity] = new class($property_value) extends EntityProxy { };
                        } elseif (is_string($entity_proxy) && class_exists($entity_proxy)) {
                            $this->entity_proxy[$entity] = new $entity_proxy($property_value);
                        }
                    }
                }
            }

            if (isset($this->entity_proxy[$entity])) {
                return $this->entity_proxy[$entity];
            } else {
                throw new RuntimeException('');
            }
        } else {
            return $this->entity_proxy[$entity];
        }
    }

    public function __get($name) 
    {
        return $this->entity($name);
    }
}