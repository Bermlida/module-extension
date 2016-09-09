<?php

namespace Vista\Router;

use Application\Core\ModelBase;
use Application\Models\Repositories\PostRepository;

trait RouteTrait extends ModelBase
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
        if (is_string($tokens) && is_string($regex)) {
            $tokens = [$tokens => $regex];
        } elseif (!is_array($tokens)) {
            throw new RuntimeException('');
        }

        $this->tokens = array_merge($this->tokens, $tokens);
        return $this;
    }

    
    public function parameter_handlers(string $source, $name, $handler = null)
    {
        if (!empty($source)) {
            if (is_string($name) || is_array($name)) {
                $name = is_string($name) ? [$name => $handler] : $name;
            
                if(in_array(false, array_map([$this, "judgeValidHandler"], $name))) {
                    throw new RuntimeException('');
                }
            } else {
                throw new RuntimeException('');
            }
        } else {
            throw new RuntimeException('');
        }

        $this->parameter_handlers[$source] = array_merge($this->parameter_handlers[$source], $name);
        return $this;
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

    // to do
    // protected  function judgeValidHandler($handler)
    // {
    //     if (is_array($handler)) {
    //         if (is_object($handler[0]) || is_string($handler[0])) {
    //             if (!isset($handler[1])|| is_string($handler[1])) {
                    
    //             } else {
                    
    //             }
    //         } else {
    //             throw new RuntimeException('');
    //         }
    //     } elseif (is_callable($handler)) {
    //         $this->handler = $handler;
    //     } else {
    //         throw new RuntimeException('');
    //     }
    // }
}

/* End of file PostModel.php */
/* Location: ./application/models/PostModel.php */