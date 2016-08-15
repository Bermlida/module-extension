<?php

namespace ModuleExtension\Foundations;

use ReflectionClass;
use RuntimeException;

abstract class Facade extends BaseFacade
{

    protected static function getFacadeSetting()
    {
        return [
            'type' => '',
            'namespace' => '\\',
            'class' => ''
        ];
    }

    protected static function getFacadeInstance()
    {
        $facade_setting = $this->getFacadeSetting();
        if ($facade_setting['type'] === '') {
            $class = new ReflectionClass(new static);
            $temp_name = str_replace("Facade", "", $class->getShortName());
            $pattern = '/\S+(Helpers)$/';
            $match = preg_match($pattern, $temp_name, $matches);

            if ($match === 0) {
                return $this->resolveFacadeInstance($class);
            } else {
                $top_namespace = str_replace("Facades", "Helpers", $class->getNamespaceName())
                return $top_namespace . "\\" . $temp_name;
            }
        } else {
            if ($facade_setting['type'] == 'function') {
                return $facade_setting['namespace'];
            } elseif ($facade_setting['type'] == 'object') {
                $instance_class = $facade_setting['namespace'] . "\\" . $facade_setting['class'];
                return new $instance_class;
            } else {
                return null;
            }
        }
    }

    protected static function resolveFacadeInstance($facade)
    {
        $facade_namespace = $facade->getNamespaceName();
        $instance_name = str_replace("Facade", "", $facade->getShortName());
                
        $pattern = '/\S+(Repository|Library)$/';
        $match = preg_match($pattern, $instance_name, $matches);
        if ($match > 0) {
            $instance_type = $matches[1];
            switch ($instance_type) {
                case "Repository":
                    $instance_namespace = str_replace("Facades", "Repositories", $facade_namespace);
                    break;
                case "Library":
                    $instance_namespace = str_replace("Facades", "Libraries", $facade_namespace);
                    break;
                default: break;
            }
            $instance_class = $instance_namespace . "\\" . $instance_name;
            return new $instance_class;
        }
        return null;
    }

    protected static function callFacadeFunction($namespace, $function, $arguments)
    {
        $function_name = $namespace . '\\' . $function;        
        if (function_exists($function_name)) {            
            switch (count($arguments)) {
                case 0:
                    return $function_name();
                case 1:
                    return $function_name($arguments[0]);
                case 2:
                    return $function_name($arguments[0], $arguments[1]);
                case 3:
                    return $function_name($arguments[0], $arguments[1], $arguments[2]);
                case 4:
                    return $function_name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                default:
                    return call_user_func_array($function_name, $arguments);
            }
        }
        throw static::failedCall($function_name, $arguments);
    }

    protected static function callFacadeMethod($instance, $method, $arguments)
    {
        switch (count($arguments)) {
            case 0:
                return $instance->$method();
            case 1:
                return $instance->$method($arguments[0]);
            case 2:
                return $instance->$method($arguments[0], $arguments[1]);
            case 3:
                return $instance->$method($arguments[0], $arguments[1], $arguments[2]);
            case 4:
                return $instance->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            default:
                return call_user_func_array([$instance, $method], $arguments);
        }
    }

    protected static function failedCall($name, $arguments)
    {
        $error_message = 'function or method call failed. name is ' . $name;
        $error_message .= ', arguments is ' . print_r($arguments, true);
        return new RuntimeException($error_message);
    }

    public static function __callStatic($method, $arguments)
    {
        $instance = static::getFacadeInstance();

        if (is_string($instance) || is_object($instance)) {
            $call = is_string($instance) ? "callFacadeFunction" : "callFacadeMethod";
            return $this->$call($instance, $method, $arguments);
        } else {
            throw static::failedCall($method, $arguments);
        }
    }
}
