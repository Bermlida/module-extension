<?php

namespace Vista\Router\Interfaces;

use Countable;
use ArrayAccess;
use IteratorAggregate;
use Vista\Router\Interfaces\RouteInterface;

interface RouteCollectionInterface extends ArrayAccess, IteratorAggregate, Countable
{
    public function setRoutes(array $routes);

    public function getRoutes();

    public function removeRoutes();

    public function setRoute(RouteInterface $route);

    public function getRoute(string $name, $methods = null);

    public function removeRoute(string $name, $methods = null);

    public function searchRoute(string $path, $methods);
}