<?php

namespace Vista\Router;

use Vista\Router\Interfaces\RouteDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class DefaultRouteDispatcher extends 
{
    protected $root_namespace;

    protected function setRootNamespace(string $root_namespace)
    {
        $this->root_namespace = $root_namespace;
    }

    public function handler(ServerRequestInterface $request)
    {
        $uri = $request->getServerParams()['REQUEST_URI'];
        $uri_path = trim(parse_url($uri)['path'], '/');
        
        $segments = explode('/', $uri_path);
        $method = array_pop($segments);
        
        foreach ($segments as $key => $segment) {
            if (!(strpos($segment, '_') === false)) {
                $segment = implode(array_map(
                    function ($segment) {
                        $segment = ucfirst(strtolower($segment));
                        return $segment;
                    },
                    explode('_', $segment)
                ));
            } else {
                $segment = ucfirst($segment);
            }
            $segments[$key] = $segment;
        }
        $class = $this->root . '/' . implode('/', $segments);

        return call_user_func([$class, $method]);
    }

}