<?php

namespace Vista\Router\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface RouteDispatcherInterface
{
    public function handle(ServerRequestInterface $request);
}
