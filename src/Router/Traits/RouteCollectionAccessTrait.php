<?php

namespace Vista\Router\Traits;

use RuntimeException;

trait RouteCollectionAccessTrait
{

    protected function setRoutes(array $routes)
    {
        $judge_result = !in_array(
            false,
            array_map(function ($route) {
                return is_a($route, "Route");
            }, $routes)
        );

        if ($judge_result) {
            $this->routes = $routes;
        } else {
            throw new RuntimeException('');
        }
    }

    protected function getRoutes()
    {
        return $this->routes;
    }

    protected function removeRoutes()
    {
        $this->routes = [];
    }
    
    protected function addRoute(Route $route)
    {
        $name = (string)$route;

        if (!isset($this->routes[$name])) {
            $this->routes[$name] = $route;
        } else {
            throw new RuntimeException('');
        }
    }

    protected function getRoute(string $name, $methods = null)
    {
        if (is_string($methods) || is_array($methods)) {
            $methods = is_string($methods) ? [$methods] : $methods;
            
            $routes = array_filter(function ($route) use ($name, $methods) {
                $compare_path = ($route->path == $name);
                $compare_methods = (count(array_diff($methods, $route->methods)) == 0);

                return $compare_path && $compare_methods;
            }, $this->routes);

            return count($routes) == 1 ? $routes[0] : null;
        } elseif (isset($this->routes[$name])) {    
            return $this->routes[$name];
        }
        // else {
        // }
        return null;
    }

    protected function removeRoute(string $name)
    {
        if (isset($this->routes[$name])) {
            unset($this->routes[$name]);
        } else {
            throw new RuntimeException('');
        }
    }


 

}
    

/* End of file UserSetting.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/Entities/UserSetting.php */
