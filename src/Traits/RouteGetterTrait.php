<?php

namespace Vista\Router\Traits;

trait RouteGetterTrait
{
    /**
     * Get the full route name.
     *
     * @return string
     */
    protected function getFullName()
    {
        $name_prefix = !empty($this->name_prefix) ? $this->name_prefix . '.' : '';

        return $name_prefix . $this->name;
    }

    /**
     * Get the full route path.
     *
     * @return string
     */
    protected function getFullPath()
    {
        $path_prefix = !empty($this->path_prefix) ? $this->path_prefix . '/' : '';

        return $path_prefix . $this->path;
    }

    /**
     * Get the full route path that the placeholder replaced with regex.
     *
     * @return string
     */
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

    /**
     * Get resolved handler.
     *
     * @return mixed
     */
    protected function getHandlerResolve()
    {
        if (method_exists($this, 'resolveHandler')) {
            return $this->resolveHandler($this->handler);
        } else {
            return $this->handler;
        }
    }

     /**
     * Get resolved param handlers.
     *
     * @return array
     */
    protected function getParamHandlersResolve()
    {
        if (method_exists($this, 'resolveHandler')) {
            $param_handlers_resolve = array_map([$this, 'resolveHandler'], $this->param_handlers);

            return $param_handlers_resolve;
        } else {
            return $this->param_handlers;
        }
    }

    /**
     * Dynamically retrieve attributes on the route.
     *
     * @param string $name
     * @return mixed
     */
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
