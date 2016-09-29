<?php

namespace Vista\Router\Traits;

use RuntimeException;

trait RouteSetterTrait
{
    abstract protected function judgeValidMethod(string $method);

    abstract protected function judgeValidRegex(string $regex);
    
    abstract protected function judgeValidHandler($handler);

    abstract protected function judgeValidSource(string $source);

    protected function setNamePrefix(string $name_prefix)
    {
        $this->name_prefix = trim($name_prefix, '.');
        return $this;
    }
    
    protected function setPathPrefix(string $path_prefix)
    {
        $this->path_prefix = trim($path_prefix, '/');
        return $this;
    }

    protected function setName(string $name)
    {
        $this->name = trim($name, '.');
        return $this;
    }

    protected function setPath(string $path)
    {
        $this->path = trim($path, '/');
        return $this;
    }

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

    protected function setMethods($methods)
    {
        $judge_result = false;
        if (is_string($methods) || is_array($methods)) {
            $methods = array_map('strtoupper', (is_string($methods) ? [$methods] : $methods));
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

    protected function setHandler($handler)
    {
        if ($this->judgeValidHandler($handler)) {
            $this->handler = $handler;
        } else {
            throw new RuntimeException('');
        }
        return $this;
    }

    protected function setParamSources($sources)
    {
        $judge_result = false;
        if (is_string($sources) || is_array($sources)) {
            $sources = array_map('strtolower', (is_string($sources) ? [$sources] : $sources));
            $judge_result = !in_array(false, array_map([$this, 'judgeValidSource'], $sources));
        }
        
        if ($judge_result) {
            $sources = array_diff($sources, $this->param_sources);
            $this->param_sources = array_merge($this->param_sources, $sources);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }
    
    protected function setParamHandlers($items, $handler = null)
    {
        $judge_result = false;
        $original_handlers = $this->param_handlers;

        if (is_string($items) || is_array($items)) {
            $handlers = is_string($items) ? [$items => $handler] : $items;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidHandler'], $handlers));
        }
        
        if ($judge_result) {
            $this->$param_handlers = array_merge($original_handlers, $handlers);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

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