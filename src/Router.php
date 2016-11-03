<?php

namespace Vista\Router;

use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Vista\Router\Interfaces\RouteDispatcherInterface;

class Router
{
    protected $root_namespace;

    protected $custom_setting;

    protected $default_route;

    protected $cache_route;

    protected $cache_path_prefix = '';

    protected $cache_name_prefix = '';

    protected $collection;

    protected $dispatcher;

    public function __construct(
        RouteCollectionInterface $collection,
        RouteDispatcherInterface $dispatcher
    ) {
        $this->collection = $collection;

        $this->dispatcher = $dispatcher;
    }

    public function setRootNamespace(string $root_namespace)
    {
        $this->root_namespace = $root_namespace;

        return $this;
    }

    public function setCustomSetting($custom_setting)
    {
        $this->custom_setting = $custom_setting;

        return $this;
    }

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

        $route->path($path)->methods($methods);        
        if (!is_null($handler)) {
            $route->handler($handler);
        }
        if (!empty($this->cache_path_prefix)) {
            $route->path_prefix($this->cache_path_prefix);
        }
        if (!empty($this->cache_name_prefix)) {
            $route->name_prefix($this->cache_name_prefix);
        }

        $this->collection[] = $route;
        $this->cache_route = $route;
        return $this;
    }
    
    public function group(string $path_prefix, callable $callback, string $name_prefix = '')
    {
        $this->cache_path_prefix = $path_prefix;
        $this->cache_name_prefix = $name_prefix;
        
        $callback($this);

        $this->cache_path_prefix = '';
        $this->cache_name_prefix = '';
        return $this;
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
        $route = is_object($this->default_route) ? clone $this->default_route : new Route();

        return $route;
    }

    public function __call($method, $arguments)
    {   // && method_exists($this->cache_route, $method)
        if (is_object($this->cache_route)) {
            switch (count($arguments)) {
                case 0:
                    $this->cache_route->$method();
                    break;
                case 1:
                    $this->cache_route->$method($arguments[0]);
                    break;
                case 2:
                    $this->cache_route->$method($arguments[0], $arguments[1]);
                    break;
                case 3:
                    $this->cache_route->$method($arguments[0], $arguments[1], $arguments[2]);
                    break;
                case 4:
                    $this->cache_route->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                    break;
                default:
                    call_user_func_array([$this->cache_route, $method], $arguments);
                    break;
            }
        }
        return $this;
    }
}