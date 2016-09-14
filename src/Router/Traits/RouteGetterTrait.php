<?php

namespace Vista\Router\Traits;

trait RouteGetterTrait
{
//     abstract protected function resolveHandler($handler);

    protected function getFullName()
    {
        return $this->name_prefix . '.' . $this->name;
    }

    protected function getFullPath()
    {
        return $this->path_prefix . '/' . $this->path;
    }

    protected function getRegex()
    {
        $regex = preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $matches) use ($this) {
                $token = $matches[1];
                return '(' .  ($this->tokens[$token] ?? '\w+') . ')';
            },
            str_replace('/', '\/', $this->getFullPath())
        );
        return $regex;
    }

    protected function getHandlerResolve()
    {
        return $this->resolveHandler($this->handler);
    }
/*
    protected function getParameterHandlersResolve()
    {
        $parameter_handlers = array_fill_keys($this->parameter_sources, []);
        foreach ($this->parameter_handlers as $source => $handlers) {
            $parameter_handlers[$source] = array_map([$this, 'resolveHandler'], $handlers);
        }
        return $parameter_handlers;
    }
    
    public function __get($name) 
    {        
        if (strpos('_', $name) !== false) {
            $name = implode(array_map(
                function ($segment) {
                    return ucfirst(strtolower($segment))
                },
                explode('_', $name)
            ));
        } else {
            $name = ucfirst($name);
        }
*/
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        } else {
            return isset($this->$name) ?? $this->$name : null;
        }
    }
}