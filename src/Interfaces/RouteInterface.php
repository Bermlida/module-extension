<?php

namespace Vista\Router\Interfaces;

use Psr\Http\Message\ServerRequestInterface;

interface RouteInterface
{
    /**
     * Use dynamic method calls for set the route attribute.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments);

    /**
     * Dynamically retrieve attributes on the route.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name);

    /**
     * Compare the route path with the requested uri.
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function matchUri(ServerRequestInterface $request);

    /**
     * Compare route allowed http method with requested http method.
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function matchMethod(ServerRequestInterface $request);

    /**
     * Pass in parameters and execute the handler.
     *
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function executeHandler(ServerRequestInterface $request);
}
