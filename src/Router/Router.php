<?php

namespace Vista\Router;

use ReflectionMethod;
use ReflectionFunction;

class Router extends Authenticatable
{
    protected $namespace = '/';
    protected $uri_paths = [];
    protected $http_methods = [];
    protected $callbacks = [];

    public function dispatch($uri_path)
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);
        $callback = $this->resolveCallback($uri['path']) ?? $this->reflectMethod($uri['path']);
        if (is_null($callback)) {

        }
    }


    protected function reflectMethod(string $uri_path)
    {
        $uri_path = explode('/', ltrim($uri_path, '/'));
        $method = array_pop($uri_path);
        $class = $this->root .  '/' .  implode('/', $uri_path);
        return new ReflectionMethod($class, $method);
    }

    protected function reflectCallback(string $uri_path)
    {
        $uri_path = ltrim($uri_path, '/');
        foreach ($this->paths as $key => $path) {            
            $path = str_replace('/', '\/', ltrim($path, '/'));
            $path = preg_replace('/\{\w+\}/', '\w+', $path);

            if (preg_match('/' + $path + '/', $uri_path) === 1) {
                $request_method = $_SERVER['REQUEST_METHOD'];
                $callbacks = $this->callbacks[$key];

                if (is_callable($callbacks[$request_method])) {
                    return new ReflectionFunction($callbacks[$request_method]);
                }
            }
        }        
        return null;
    }
}