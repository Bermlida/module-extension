<?php

namespace App\Models\Foundations;

use ReflectionClass;
use Illuminate\Support\Facades\Facade as BaseFacade;

abstract class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return "";
    }

    public static function getFacadeRoot()
    {
        $facade_accessor = static::getFacadeAccessor();
        if ($facade_accessor === "") {
            $facade_name = (new ReflectionClass(new static))->getShortName();
            $instance_name = str_replace("Facade", "", $facade_name);
            return static::resolveFacadeInstance($instance_name);
        } else {
            return parent::resolveFacadeInstance($facade_accessor);
        }
    }

    protected static function resolveFacadeInstance($instance_name)
    {
        $pattern = '/\S+(Repository|Library)$/';
        $match = preg_match($pattern, $instance_name, $matches);
        if ($match > 0) {
            $instance_type = $matches[1];
            switch ($instance_type) {
                case "Repository":
                    $instance_name = "App\\Models\\Repositories\\" . $instance_name;
                    break;
                case "Library":
                    $instance_name = "App\\Models\\Libraries\\" . $instance_name;
                    break;
                default: break;
            }
            return new $instance_name;
        }
        return null;
    }
}
