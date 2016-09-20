<?php

namespace Vista\Router;

use RuntimeException;
use Vista\Router\RouteCollection;
use Vista\Router\RouteDispatcher;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    protected $root_namespace;

    protected $custom_setting;

    protected $cache_path;

    protected $cache_methods;

    protected $collection = new RouteCollection();

    protected $dispatcher = new RouteDispatcher();

    public function rule(string $path, $method, array $options = [])
    {
        $route = $this->cacheRoute();
        $rule_options = ['path' => $path, 'methods' => $methods];
        $route_options = array_merge($options, $rule_options);

        foreach ($route_options as $key => $option) {
            $this->$key($option);
        }
        
        $this->cache_path = $route->path;
        $this->cache_methods = $route->methods;
        return $this;
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

    public function dispatch(ServerRequestInterface $request)
    {
        if ($this->dispatcher->custom($this->custom_setting)->handle($request)->executed()) {
            return $this->dispatcher->result();
        } elseif ($this->dispatcher->rule($this->collection)->handle($request)->executed()) {
            return $this->dispatcher->result();
        } elseif ($this->dispatcher->default($this->root_namespace)->handle($request)->executed()) {
            return $this->dispatcher->result();
        } else {
            throw new RuntimeException('');
        }
    }

    public function __call($method, $arguments)
    {
        if (is_object($this->cache_route) && method_exists($this->cache_route, $method)) {            
            switch (count($arguments)) {
                case 0:
                    return $this->$cache_route->$method();
                case 1:
                    return $this->$cache_route->$method($arguments[0]);
                case 2:
                    return $this->$cache_route->$method($arguments[0], $arguments[1]);
                case 3:
                    return $this->$cache_route->$method($arguments[0], $arguments[1], $arguments[2]);
                case 4:
                    return $this->$cache_route->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                default:
                    return call_user_func_array([$this->cache_route, $method], $arguments);
            }
        }


        return $this;
    }
}