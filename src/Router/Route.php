<?php

namespace Vista\Router;

class Route implements RouteInterface
{
    use RouteProperty, RouteSetter;

    protected  function judgeValidRegex($regex)
    {
        return is_string($regex);
    }

    protected  function judgeValidSource(string $source)
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
