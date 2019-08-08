<?php

namespace Vista\Router\Traits;

use RuntimeException;

trait RouteSetterTrait
{
    /**
     * Judge the Http method is valid.
     *
     * @param string $method
     * @return bool
     */
    abstract protected function judgeValidMethod(string $method);

    /**
     * Judge the regex is valid.
     *
     * @param string $regex
     * @return bool
     */
    abstract protected function judgeValidRegex(string $regex);
    
    /**
     * Judge the handler is valid.
     *
     * @param mixed $handler
     * @return bool
     */
    abstract protected function judgeValidHandler($handler);

    /**
     * Judge the source is valid.
     *
     * @param string $source
     * @return bool
     */
    abstract protected function judgeValidSource(string $source);

    /**
     * set the route name prefix.
     *
     * @param string $name_prefix
     * @return $this
     */
    protected function setNamePrefix(string $name_prefix)
    {
        $this->name_prefix = trim($name_prefix, '.');

        return $this;
    }
    
    /**
     * set the route path prefix.
     *
     * @param string $path_prefix
     * @return $this
     */
    protected function setPathPrefix(string $path_prefix)
    {
        $this->path_prefix = trim($path_prefix, '/');

        return $this;
    }

    /**
     * set the route name.
     *
     * @param string $name
     * @return $this
     */
    protected function setName(string $name)
    {
        $this->name = trim($name, '.');

        return $this;
    }

    /**
     * set the route path.
     *
     * @param string $path
     * @return $this
     */
    protected function setPath(string $path)
    {
        $this->path = trim($path, '/');

        return $this;
    }

    /**
     * set the placeholder token name and its regex in route path.
     *
     * @param string|array|mixed $tokens
     * @param mixed|null $regex
     * @return $this
     * @throws RuntimeException
     */
    protected function setTokens($tokens, $regex = null)
    {
        $judge_result = false;

        if (is_string($tokens) || is_array($tokens)) {
            $regexes = is_string($tokens) ? [$tokens => $regex] : $tokens;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidRegex'], $regexes));
        }
        
        if ($judge_result) {
            $this->tokens = array_merge($this->tokens, $regexes);

            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    /**
     * set the route HTTP methods.
     *
     * @param string|array|mixed $methods
     * @return $this
     * @throws RuntimeException
     */
    protected function setMethods($methods)
    {
        $judge_result = false;

        if (is_string($methods) || is_array($methods)) {
            // $methods = array_map('strtoupper', (is_string($methods) ? [$methods] : $methods));
            $methods = is_string($methods) ? [$methods] : $methods;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidMethod'], $methods));
        }
        
        if ($judge_result) {
            $methods = array_diff($methods, $this->methods);
            $this->methods = array_merge($this->methods, $methods);

            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    /**
     * set the handler deal with request if match route.
     *
     * @param mixed $handler
     * @return $this
     * @throws RuntimeException
     */
    protected function setHandler($handler)
    {
        if ($this->judgeValidHandler($handler)) {
            $this->handler = $handler;
        } else {
            throw new RuntimeException('');
        }

        return $this;
    }

    /**
     * set the source of the parameter that should be preprocessed.
     *
     * @param string|array|mixed $items
     * @param mixed|null $source
     * @return $this
     * @throws RuntimeException
     */
    protected function setParamSources($items, $source = null)
    {
        $judge_result = false;
        $original_sources = $this->param_sources;

        if (is_string($items) || is_array($items)) {
            $sources = is_string($items) ? [$items => $source] : $items;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidSource'], $sources));
        }
        
        if ($judge_result) {
            $this->param_sources = array_merge($original_sources, $sources);

            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    /**
     * set the handlers for preprocessing specific parameters.
     *
     * @param string|array|mixed $items
     * @param mixed|null $handler
     * @return $this
     * @throws RuntimeException
     */
    protected function setParamHandlers($items, $handler = null)
    {
        $judge_result = false;
        $original_handlers = $this->param_handlers;

        if (is_string($items) || is_array($items)) {
            $handlers = is_string($items) ? [$items => $handler] : $items;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidHandler'], $handlers));
        }
        
        if ($judge_result) {
            $this->param_handlers = array_merge($original_handlers, $handlers);

            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    /**
     * Handle dynamic method calls in the route setter.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, '_') !== false) {
            $name = implode(array_map(
                function ($segment) {
                    return ucfirst(strtolower($segment));
                },
                explode('_', $name)
            ));
        } else {
            $name = ucfirst($name);
        }

        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            switch (count($arguments)) {
                case 0:
                    return $this->$method();
                case 1:
                    return $this->$method($arguments[0]);
                case 2:
                    return $this->$method($arguments[0], $arguments[1]);
                case 3:
                    return $this->$method($arguments[0], $arguments[1], $arguments[2]);
                case 4:
                    return $this->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                default:
                    return call_user_func_array([$this, $method], $arguments);
            }
        } else {
            throw new RuntimeException('');
        }
    }
}
