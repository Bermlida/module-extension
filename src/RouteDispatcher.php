<?php

namespace Vista\Router;

use ReflectionMethod;
use Psr\Http\Message\ServerRequestInterface;
use Vista\Router\Interfaces\RouteModelInterface;
use Vista\Router\Prototypes\RouteDispatcherPrototype;

class RouteDispatcher extends RouteDispatcherPrototype
{
    protected function getClass(ServerRequestInterface $request)
    {
        $uri = $request->getServerParams()['REQUEST_URI'];
        $uri_path = parse_url($uri)['path'];
        $segments = explode('/', trim($uri_path, '/'));

        array_pop($segments);
        array_walk($segments, [$this, 'handleSegment']);

        return implode('\\', $segments);
    }
    
    protected function getMethod(ServerRequestInterface $request)
    {
        $uri = $request->getServerParams()['REQUEST_URI'];
        $uri_path = parse_url($uri)['path'];
        $segments = explode('/', trim($uri_path, '/'));

        $segment = array_pop($segments);
        $request_method = $request->getServerParams()['REQUEST_METHOD'];

        return strtolower($request_method) . $this->handleSegment($segment);
    }

    protected function bindArguments(array $handler, ServerRequestInterface $request)
    {
        $class_method = new ReflectionMethod($handler[0], $handler[1]);
        $parameters = $class_method->getParameters();
        
        if (!empty($parameters)) {
            $params = $this->getSourceParams($request);

            if (!is_null($reflector = $parameters[0]->getClass())) {
                if ($reflector->implementsInterface(RouteModelInterface::class)) {
                    $constructor = $reflector->getConstructor();
                    if (!is_null($constructor)) {
                        foreach ($constructor->getParameters() as $key => $parameter) {
                            if (isset($params[$parameter->name])) {
                                $value = $params[$parameter->name];
                                $arguments[$key] = $value;
                            }
                        }
                        $arguments = [$reflector->newInstanceArgs(($arguments ?? []))];
                    }
                } elseif ($reflector->implementsInterface(ServerRequestInterface::class)) {
                    $arguments = [$request];
                }
            } else {
                foreach ($parameters as $key => $parameter) {
                    if (isset($params[$parameter->name])) {
                        $value = $params[$parameter->name];
                        $arguments[$key] = $value;
                    }
                }
            }
        }

        return $arguments ?? [];
    }
    
    protected function getSourceParams(ServerRequestInterface $request)
    {
        $request_method = $request->getServerParams()['REQUEST_METHOD'];

        switch (strtolower($request_method)) {
            case 'options':
            case 'head':
            case 'get':
                return array_merge(
                    $request->getParsedBody(),
                    $request->getQueryParams()
                );
            case 'post':
            case 'put':
            case 'delete':
                return array_merge(
                    $request->getQueryParams(),
                    $request->getParsedBody()
                );
            default:
                return [];
        }
    }

    protected function handleSegment(string &$segment)
    {
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

        return $segment;
    }
}