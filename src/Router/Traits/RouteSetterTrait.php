<?php

namespace Vista\Router\Traits;

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
            $tokens = is_string($tokens) ? [$tokens => $regex] : $tokens;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidRegex'], $tokens));
        }
        
        if ($judge_result) {
            $this->tokens = array_merge($this->tokens, $tokens);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    protected function setMethods($methods)
    {
        $judge_result = false;
        if (is_string($methods) || is_array($methods)) {
            $methods = is_string($methods) ? [$methods] : $methods;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidMethod'], $methods));
        }
        
        if ($judge_result) {
            $methods = array_map('strtoupper', array_diff($methods, $this->methods));
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
            $sources = is_string($sources) ? [$sources] : $sources;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidSource'], $sources));
        }
        
        if ($judge_result) {
            $sources = array_map('strtolower', array_diff($sources, $this->param_sources));
            $this->param_sources = array_merge($this->param_sources, $sources);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    protected function setParamSourceHandlers(string $source, $handler = null)
    {
        $judge_result = false;
        if (is_string($tokens) || is_array($tokens)) {
            $tokens = is_string($tokens) ? [$tokens => $regex] : $tokens;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidRegex'], $tokens));
        }
        
        if ($judge_result) {
            $this->tokens = array_merge($this->tokens, $tokens);
            return $this;
        } else {
            throw new RuntimeException('');
        }
/*
        $judge_source = $this->judgeValidSource($source);
        $judge_handler = is_null($handler) ? true : $this->judgeValidHandler($handler);
        $judge_item_handlers = empty($item_handlers) ? true : !in_array(false, array_map([$this, 'judgeValidHandler'], $item_handlers));
        if ($judge_source && $judge_handler && $judge_item_handlers) {            
            $source = strtolower($source);
            $original_handlers = $this->parameters[$source] ?? [];
            if (!is_null($handler) || !empty($item_handlers)) {
                if (!is_null($handler)) {
                    $new_handlers['handler'] = $handler;
                }
                if (!empty($item_handlers)) {
                    $new_handlers['item_handlers'] = $item_handlers;
                }
                $this->parameters[$source] = array_merge($original_handlers, $new_handlers);
            } else {
                $this->parameters[$source] = $original_handlers;
            }
            return $this;
        } else {
            throw new RuntimeException('');
        }
*/
    }

    protected function setParameterSources($sources)
    {
        $judge_result = false;
        if (is_string($sources) || is_array($sources)) {
            $sources = is_string($sources) ? [$sources] : $sources;
            $judge_result = !in_array(false, array_map([$this, 'judgeValidSource'], $sources));
        }
        
        if ($judge_result) {
            $sources = array_map('strtolower', array_diff($sources, $this->parameter_sources));
            $this->parameter_sources = array_merge($this->parameter_sources, $sources);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    protected function setParameterHandlers(string $source, $name, $handler = null)
    {
        $judge_result = false;
        if ($this->judgeValidSource($source)) {
            $source = strtolower($source);
            $old_handlers = $this->parameter_handlers[$source] ?? [];

            if (is_string($name) || is_array($name)) {
                $name = is_string($name) ? [$name => $handler] : $name;
                $judge_result = !in_array(false, array_map([$this, 'judgeValidHandler'], $name));
            }
        }
        
        if ($judge_result) {
            $this->parameter_handlers[$source] = array_merge($old_handlers, $name);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    public function __call($name, $arguments) 
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