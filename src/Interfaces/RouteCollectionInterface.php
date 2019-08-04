<?php

namespace Vista\Router\Interfaces;

use Countable;
use ArrayAccess;
use IteratorAggregate;
use Vista\Router\Interfaces\RouteInterface;

interface RouteCollectionInterface extends ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Set routes.
     *
     * @param array $routes
     * @return void
     */
    public function setRoutes(array $routes);

    /**
     * Get routes.
     *
     * @return array
     */
    public function getRoutes();

    /**
     * Remove routes.
     *
     * @return void
     */
    public function removeRoutes();

    /**
     * Set route to the routes.
     *
     * @param RouteInterface $route
     * @return void
     */
    public function setRoute(RouteInterface $route);

    /**
     * Get route for the name(or include methods).
     *
     * @param string $name
     * @param mixed|null $methods
     * @return RouteInterface|null
     */
    public function getRoute(string $name, $methods = null);

     /**
     * Remove route for the name(or include methods).
     *
     * @param string $name
     * @param mixed|null $methods
     * @return void
     */
    public function removeRoute(string $name, $methods = null);

    /**
     * Search route for the path and methods.
     *
     * @param string $path
     * @param mixed $methods
     * @return int
     */
    public function searchRoute(string $path, $methods);
}