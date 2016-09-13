<?php

namespace Vista\Router\Traits;

trait RouteSetterTrait
{
    abstract protected function judgeValidMethod(string $method);

    abstract protected function judgeValidRegex(string $regex);

    abstract protected function judgeValidSource(string $source);
    
    abstract protected function judgeValidHandler($handler);

    public function namePrefix(string $name_prefix)
    {
        $this->name_prefix = $name_prefix;
        return $this;
    }
    
    public function pathPrefix(string $path_prefix)
    {
        $this->path_prefix = $path_prefix;
        return $this;
    }

    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function path(string $path)
    {
        $this->path = $path;
        return $this;
    }

    public function tokens($tokens, $regex = null)
    {
        $judge_result = false;
        if (is_string($tokens) || is_array($tokens)) {
            $tokens = is_string($tokens) ? [$tokens => $regex] : $tokens;
            $judge_result = !in_array(false, array_map([$this, "judgeValidRegex"], $tokens));
        }
        
        if ($judge_result) {
            $this->tokens = array_merge($this->tokens, $tokens);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    public function methods($methods)
    {
        $judge_result = false;
        if (is_string($methods) || is_array($methods)) {
            $methods = is_string($methods) ? [$methods] : $methods;
            $judge_result = !in_array(false, array_map([$this, "judgeValidMethod"], $methods));
        }
        
        if ($judge_result) {
            $methods = array_map("strtoupper", array_diff($methods, $this->methods));
            $this->methods = array_merge($this->methods, $methods);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }

    public function handler($handler)
    {
        if ($this->judgeValidHandler($handler)) {
            $this->handler = $handler;
        } else {
            throw new RuntimeException('');
        }
        return $this;
    }

    public function parameter_sources($sources)
    {
        $judge_result = false;
        if (is_string($sources) || is_array($sources)) {
            $sources = is_string($sources) ? [$sources] : $sources;
            $judge_result = !in_array(false, array_map([$this, "judgeValidSource"], $sources));
        }
        
        if ($judge_result) {
            $sources = array_map("strtolower", array_diff($sources, $this->parameter_sources));
            $this->parameter_sources = array_merge($this->parameter_sources, $sources);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }
    
    public function parameter_handlers(string $source, $name, $handler = null)
    {
        if ($this->judgeValidSource($source)) {
            $source = strtolower($source);
            $old_handlers = $this->parameter_handlers[$source] ?? [];

            $judge_result = false;
            if (is_string($name) || is_array($name)) {
                $name = is_string($name) ? [$name => $handler] : $name;
                $judge_result = !in_array(false, array_map([$this, "judgeValidHandler"], $name));
            }
        }
        
        if ($judge_result) {
            $this->parameter_handlers[$source] = array_merge($old_handlers, $name);
            return $this;
        } else {
            throw new RuntimeException('');
        }
    }
}