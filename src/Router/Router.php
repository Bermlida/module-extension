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
/*
    protected $cache_path;
*/
    protected $default_route;

    protected $cache_route;

    protected $collection = new RouteCollection();

    protected $dispatcher = new RouteDispatcher();

    public function options(string $path, $handler = null)
    {
        return $this->route($path, 'options', $handler);
    }

    public function head(string $path, $handler = null)
    {
        return $this->route($path, 'head', $handler);
    }

    public function get(string $path, $handler = null)
    {
        return $this->route($path, 'get', $handler);
    }

    public function put(string $path, $handler = null)
    {
        return $this->route($path, 'put', $handler);
    }

    public function delete(string $path, $handler = null)
    {
        return $this->route($path, 'delete', $handler);
    }

    public function post(string $path, $handler = null)
    {
        return $this->route($path, 'post', $handler);
    }

    public function patch(string $path, $handler = null)
    {
        return $this->route($path, 'patch', $handler);
    }

    public function route(string $path, $methods, $handler = null)
    {
        $route = $this->registerRoute();

        $route->path($path)
                    ->methods($methods);
        
        if (!is_null($handler)) {
            $route->handler($handler);
        }

        $this->collection[] = $route;
        $this->cache_route = $route;
        return $this;
    }
    
    public function group(string $name_prefix, string $path_prefix, callable $callback)
    {
        $route = $this->registerRoute();

        $route->name_prefix($name_prefix)
                    ->path_prefix($path_prefix);
        $callback($route);
        return $route
    }

    public function default($callback = null)
    {
        $route = $this->registerRoute();

        $this->cache_route = $route;
        if (is_callable($callback)) {
            $callback($this);
        }

        $this->default_route = $route;
        return $this;
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

    protected function registerRoute()
    {

    }

    public function __call($method, $arguments)
    {
        if (is_object($this->cache_route) && method_exists($this->cache_route, $method)) {            
            switch (count($arguments)) {
                case 0:
                    $this->cache_route->$method();
                case 1:
                    $this->cache_route->$method($arguments[0]);
                case 2:
                    $this->cache_route->$method($arguments[0], $arguments[1]);
                case 3:
                    $this->cache_route->$method($arguments[0], $arguments[1], $arguments[2]);
                case 4:
                    $this->cache_route->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                default:
                    call_user_func_array([$this->cache_route, $method], $arguments);
            }
        }
        return $this;
    }
}