<?php

namespace Vista\Router;

// use Application\Core\ModelBase;
// use Application\Models\Repositories\PostRepository;

trait RouteSetter extends ModelBase
{
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

    public function handler($handler)
    {
        if ($this->judgeValidHandler($handler)) {
            $this->handler = $handler;
        } else {
            throw new RuntimeException('');
        }
        return $this;
    }
}