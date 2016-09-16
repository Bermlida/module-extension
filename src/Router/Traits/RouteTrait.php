<?php

namespace Vista\Router\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait RouteTrait
{
    abstract protected function resolveHandler($handler);
    
    abstract protected function resolveSources(ServerRequestInterface $request);

    abstract protected function handleParams(array $params);

    abstract protected function bindArguments(array $params);

    abstract protected function callHandler(Callable $handler, array $arguments);


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