<?php

namespace Vista\Router\Tests\Handlers;

use Psr\Http\Message\ServerRequestInterface;

class TestHandler
{
    public function process(ServerRequestInterface $request)
    {
        return $request;
            // $this->getDefaultHandleParams(),, $param, $request, $stub[]
    }
}