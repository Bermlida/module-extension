<?php

namespace Vista\Router\Traits;

use RuntimeException;
use Vista\Router\Interfaces\RouteInterface;

trait RouteCollectionTrait
{
    /**
     * Set routes.
     *
     * @param array $routes
     * @return void
     */
    public function setRoutes(array $routes)
    {
        $this->routes = [];

        array_walk($routes, [$this, "setRoute"]);
    }

    /**
     * Get routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Remove routes.
     *
     * @return void
     */
    public function removeRoutes()
    {
        $this->routes = [];
    }

    /**
     * Set route to the routes.
     *
     * @param RouteInterface $route
     * @return void
     */
    public function setRoute(RouteInterface $route)
    {
        $name_is_empty = empty($full_name = $route->full_name);

        foreach ($this->routes as $current_route) {
            $compare_path = $current_route->full_path == $route->full_path;
            $compare_methods = !empty(array_intersect($route->methods, $current_route->methods));
            $compare_name = ($current_route->full_name == $route->full_name) && !$name_is_empty;

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

    /**
     * Get route for the name(or include methods).
     *
     * @param string $name
     * @param mixed|null $methods
     * @return RouteInterface|null
     */
    public function getRoute(string $name, $methods = null)
    {
        if (is_string($methods) || is_array($methods)) {
            if (($key = $this->searchRoute($name, $methods)) >= 0) {
                return $this->routes[$key];
            }
        } elseif (isset($this->routes[$name])) {    
            return $this->routes[$name];
        }

        return null;
    }

    /**
     * Remove route for the name(or include methods).
     *
     * @param string $name
     * @param mixed|null $methods
     * @return void
     */
    public function removeRoute(string $name, $methods = null)
    {
        if (is_string($methods) || is_array($methods)) {
            $key = $this->searchRoute($name, $methods);
        } elseif (isset($this->routes[$name])) {
            $key = $name;
        }

        if (isset($key) && $key >= 0) {
            unset($this->routes[$key]);
        } else {
            throw new RuntimeException('');
        }
    }

    /**
     * Search route for the path and methods.
     *
     * @param string $path
     * @param mixed $methods
     * @return int
     */
    public function searchRoute(string $path, $methods)
    {
        if (is_string($methods) || is_array($methods)) {
            $methods = is_string($methods) ? [$methods] : $methods;
            $path = trim($path, '/');
            
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
