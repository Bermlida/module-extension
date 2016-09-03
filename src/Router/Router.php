<?php

namespace Vista\Router;

//use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;

class Router extends Authenticatable
{
    //protected $namespace = '/';
    protected $uri_rules = [];
    protected $callbacks = [];
    protected $http_methods = [];

    public function dispatch($uri_path)
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);

        $path = trim($uri['path'], '/');
        if (($index = $this->compareUri($path)) < 0) {
            $processor = $this->resolveUri($path);
            $reflector = $this->reflectMethod($processor->class, $processor->method);
            $params = $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : $_GET;
        } else {
            $reflector = $this->reflectCallback($index);
            $params = $this->getParamsByUri($index, $path);
        }

        $arguments = $this->bindArguments($reflector, $params);
        $reflector->invokeArgs($arguments);
    }

    protected function compareUri(string $uri)
    {
        foreach ($this->rules as $key => $rule) {
            $pattern = str_replace('/', '\/', $rule);
            $pattern = '/' + preg_replace('/\{\w+\}/', '\w+', $rule) + '/';

            if (preg_match($pattern, $uri) === 1) {
                return $key;
            }
        }
        return -1;
    }

    protected function resolveUri(string $uri)
    {
        $segments = explode('/', $uri);
        $method = array_pop($segments);
        
        foreach ($segments as $key => $segment) {
            if (!(strpos($segment, '_') === false)) {
                $segment = implode(array_map(function ($segment) {
                    $segment = ucfirst(strtolower($segment));
                    return $segment;
                }, explode('_', $segment)));
            } else {
                $segment = ucfirst($segment);
            }
            $segments[$key] = $segment;
        }
        $class = $this->root . '/' . implode('/', $segments);

        return (object)(['class' => $class, 'method' => $method]);
    }

    protected function reflectMethod(string $class, string $method)
    {
        $object = new $class;
        $reflector_method = new ReflectionMethod($object, $method);
        $closure = $reflector_method->getClosure($object);
        return new ReflectionFunction($closure);
    }

    protected function reflectCallback(int $index)
    {
        $request_method = $_SERVER['REQUEST_METHOD'];
        $callbacks = array_change_key_case($this->callbacks[$index], CASE_UPPER);
        $callback = $callbacks[$request_method];

        if (is_callable($callback)) {
            return new ReflectionFunction($callback);
        } elseif (is_string($callback) && !(strpos($callback, '::') === false)) {
            $segments = explode('::', $callback);
            return $this->reflectMethod($segments[0], $segments[1]);
        }
        return null;
    }

    protected function getParamsByUri(string $index, string $uri)
    {
        $rule_segments = explode('/', $this->rules[$index]);
        $uri_segments = explode('/', $uri);

        foreach ($rule_segments as $index => $segment) {
            if (preg_match('/\{(\w+)\}/', $segment, $matches) === 1) {
                $key = $matches[1];
                $value = $uri_segments[$index];
                $params[$key] = $value;
            }
        }

        return $params ?? [];
    }

    protected function bindArguments(ReflectionFunction $reflector, array $params)
    {
        $parameters = $reflector->getParameters();
        
        if (count($parameters) > 0) {
            $reflector = $parameters[0]->getClass();
            $interface_constraint = "";
            
            if (!is_null($reflector) && $reflector->implementsInterface($interface_constraint)) {
                $object = new $reflector->getName();
                foreach ($params as $key => $value) {
                    $object->$key = $value;
                }
                $arguments[] = $object;
            } else {
                foreach ($parameters as $key => $parameter) {
                    if (isset($params[$parameter->name])) {
                        $value = $params[$parameter->name];
                        $arguments[$key] = $value;
                    }
                }
            }
        }

        return $arguments ?? [];
    }
}