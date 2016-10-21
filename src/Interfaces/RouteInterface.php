<?php

namespace Vista\Router\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    public function __call($name, $arguments);

    public function __get($name);

    public function matchUri(ServerRequestInterface $request);

    public function matchMethod(ServerRequestInterface $request);

    public function executeHandler(ServerRequestInterface $request);
}
