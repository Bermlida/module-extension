<?php

namespace Vista\Router;

use RuntimeException;
use ReflectionMethod;
use ReflectionFunction;

class Router
{
    public function route(string $name, string $path, string $method)
    {
        $route = new Route();
        $route = $route->name($name)
                                    ->path($path)
                                    ->method($method);
        return $route;
    }

    public function default($setting)
    {
        $route = $this->default_route ?? new Route();
        if (is_array($setting)) {
            foreach ($setting as $property => $parameters) {
                call_user_func_array([$route, $property], (array)$parameters);
            }
        } elseif (is_callable($setting)) {
            $setting($route);
        }
        return $route;
    }
    
    public function group(string $name_prefix, string $path_prefix, callable $callback)
    {
        $route = new Route();
        $callback($route);
        return $route
    }
}