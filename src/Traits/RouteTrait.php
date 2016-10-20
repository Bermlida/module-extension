<?php

namespace Vista\Router\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait RouteTrait
{
    abstract protected function resolveHandler($handler);
    
    abstract protected function resolveSources(ServerRequestInterface $request);

    abstract protected function handleParams(array $params);

    abstract protected function bindArguments(array $params);

    abstract protected function callHandler(callable $handler, array $arguments);

    public function matchUri(ServerRequestInterface $request)
    {
        $uri = $request->getServerParams()['REQUEST_URI'];
        $uri_path = trim(parse_url($uri)['path'], '/');

        return preg_match('/' . $this->full_regex . '/', $uri_path) === 1;
    }

    public function matchMethod(ServerRequestInterface $request)
    {
        $request_method = $request->getServerParams()['REQUEST_METHOD'];

        return in_array($request_method, $this->methods);
    }

    public function executeHandler(ServerRequestInterface $request)
    {
        $handler = $this->resolveHandler($this->handler);

        if (!empty($this->param_sources)) {
            $params = $this->resolveSources($request);
            if (!empty($this->param_handlers)) {
                $params = $this->handleParams($params);
            }

            $arguments = $this->bindArguments($params);
        } else {
            $arguments = [];
        }
        
        return $this->callHandler($handler, $arguments);
    }
}