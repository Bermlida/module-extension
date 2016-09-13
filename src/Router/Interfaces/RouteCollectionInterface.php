<?php

namespace Vista\Router\Interfaces;

use Countable;
use ArrayAccess;
use IteratorAggregate;

interface RouteCollectionInterface extends ArrayAccess, IteratorAggregate, Countable
{
    public function setRoutes(array $routes);

    public function getRoutes();

    public function removeRoute(string $name, $methods = null);

    public function setRoute(Route $route);

    public function getRoute(string $name, $methods = null);

    public function removeRoute(string $name, $methods = null);

    public function searchRoute(string $path, $methods);
}