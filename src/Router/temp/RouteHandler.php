<?php

namespace Vista\Router;

use Vista\Router\Traits\RouteSetterTrait;
use Vista\Router\Traits\RouteGetterTrait;

class RouteHandler implements RouteHandlerInterface
{
    use RouteSetterTrait, RouteGetterTrait;

    protected $source;

    protected $processor = [];

    protected $param_processors = [];

    // protected $param_sources = [];

    public function param_sources($sources)
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

    protected function judgeValidRegex(string $regex)
    {
        return true;
    }

    protected function judgeValidSource(string $source)
    {
        $source = strtolower($source);

        switch ($source) {
            case "uri":
            case "get":
            case "post":
            case "file":
            case "cookie":
                return true;
            default:
                return false;
        }
    }
    
    protected function judgeValidHandler($handler)
    {
        if (is_array($handler)) {
            if (is_object($handler[0]) || is_string($handler[0])) {
                if (!isset($handler[1])|| is_string($handler[1])) {
                    return true;
                }
            }
        } elseif (is_callable($handler)) {
            return true;
        }
        return false;
    }
}
