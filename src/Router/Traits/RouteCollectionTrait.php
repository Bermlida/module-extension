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
        foreach ($this->routes as $current_route) {
            $compare_path = $current_route->full_path == $route->full_path;
            $compare_methods = !empty(array_intersect($route->methods, $current_route->methods));
            $compare_name = ($current_route->full_name == $route->full_name) && !empty($route->full_name);

            if ($compare_path && $compare_methods && $compare_name) {
                throw new RuntimeException('');
            }
        }
        
        if (!empty($route->full_name)) {
            $this->routes[$route->full_name] = $route;
        } else {
            $this->routes[] = $route;
        }
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

        if ((is_numeric($key) && $key > 0) || isset($key)) {
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
