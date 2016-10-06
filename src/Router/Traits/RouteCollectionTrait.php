<?php

namespace Vista\Router\Traits;

use RuntimeException;
use Vista\Router\Interfaces\RouteInterface;

trait RouteCollectionTrait
{
    public function setRoutes(array $routes)
    {
        array_walk($routes, [$this, "setRoute"]);
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function removeRoutes()
    {
        $this->routes = [];
    }
    
    public function setRoute(RouteInterface $route)
    {
        $name_is_empty = empty($full_name = $route->full_name);

        foreach ($this->routes as $current_route) {
            $compare_path = $current_route->full_path == $route->full_path;
            $compare_methods = !empty(array_intersect($route->methods, $current_route->methods));
            $compare_name = ($current_route->full_name == $route->full_name) && !$full_name_empty;

            if ($compare_path && $compare_methods && $compare_name) {
                throw new RuntimeException('');
            }
        }

        $this->routes[] = $route;
/*
        if (!$full_name_empty) {
            $this->routes[$route->full_name] = $route;
        } else {
            $this->routes[] = $route;
        }
*/
    }

    public function getRoute(string $name, $methods = null)
    {
        if (is_string($methods) || is_array($methods)) {
            if (($key = $this->searchRoute($name, $methods)) > 0) {
                return $this->routes[$key];
            }
        } elseif (isset($this->routes[$name])) {    
            return $this->routes[$name];
        }
        return null;
    }

    public function removeRoute(string $name, $methods = null)
    {
        if (is_string($methods) || is_array($methods)) {
            $key = $this->searchRoute($name, $methods);
        } elseif (isset($this->routes[$name])) {
            $key = $name;
        }

        if (isset($key) && $key > 0) {
            unset($this->routes[$key]);
        } else {
            throw new RuntimeException('');
        }
    }

    public function searchRoute(string $path, $methods)
    {
        if (is_string($methods) || is_array($methods)) {
            $methods = is_string($methods) ? [$methods] : $methods;
            
            foreach ($this->routes as $key => $route) {
                $compare_path = $route->full_path == $path;
                $compare_methods = count(array_diff($methods, $route->methods)) == 0;

                if ($compare_path && $compare_methods) {
                    return $key;
                }
            }
        }
        return -1;
    }
}
