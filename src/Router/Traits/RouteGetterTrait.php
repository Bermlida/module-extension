<?php

namespace Vista\Router\Traits;

trait RouteGetterTrait
{
    protected function getFullName()
    {
        $name_prefix = !empty($this->name_prefix) ? $this->name_prefix . '.' : '';
        return $name_prefix . $this->name;
    }

    protected function getFullPath()
    {
        $path_prefix = !empty($this->path_prefix) ? $this->path_prefix . '/' : '';
        return $path_prefix . $this->path;
    }

    protected function getFullRegex()
    {
        $tokens = $this->tokens;
        $regex = preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $matches) use ($tokens) {
                $token = $matches[1];
                return '(' .  ($tokens[$token] ?? '\w+') . ')';
            },
            str_replace('/', '\/', $this->getFullPath())
        );
        return $regex;
    }

    protected function getHandlerResolve()
    {
        if (method_exists($this, 'resolveHandler')) {
            return $this->resolveHandler($this->handler);
        } else {
            return $this->handler;
        }
    }

    protected function getParamHandlersResolve()
    {
        if (method_exists($this, 'resolveHandler')) {
            $param_handlers_resolve = array_map([$this, 'resolveHandler'], $this->param_handlers);
            return $param_handlers_resolve;
        } else {
            return $this->param_handlers;
        }
    }

    public function __get($name) 
    {        
        if (strpos($name, '_') !== false) {
            $method_name = implode(array_map(
                function ($segment) {
                    return ucfirst(strtolower($segment));
                },
                explode('_', $name)
            ));
        } else {
            $method_name = ucfirst($name);
        }

        $method = 'get' . $method_name;
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return isset($this->$name) ? $this->$name : null;
        }
    }
}