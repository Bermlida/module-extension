<?php

namespace Vista\Router;

use RuntimeException;

trait RegisterRuleFeature extends Model
{

    public function route(string $name, string $path, $handler)
    {
        $route = new Route();
        $route = $route->name($name)
                                    ->path($path)
                                    ->handler($handler);
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
    
/*
    public function register(string $rule, $method, $callback = null)
    {
        if (is_array($method)) {
            foreach ($method as $verb => $callback) {
                $verb = strtolower($verb);
                $this->$verb($rule, $callback);
            }
        } elseif (is_string($method) && $method != '') {
                $method = strtolower($method);
            if (is_string($callback) || is_callable($callback)) {
                $this->$method($rule, $callback);
            }
        }
    }

    public function __call($method, $arguments)
    {
        $valid_verb = ["post", "get", "put", "delete", "header", "patch", "options"];
        
        if (in_array($method, $valid_verb)) {
            $method = strtoupper($method);
            $keys = array_keys($this->rules, $arguments[0]);

            if (count($keys) > 0) {
                $key = current($keys);
                $this->callbacks[$key][$method] = $arguments[1];
            } else {
                $callbacks[$method] = $arguments[1];
                $this->rules[] = $arguments[0];
                $this->callbacks[] = $callbacks;
            }
        } else {
            throw new RuntimeException('');
        }
    }
    */
}
