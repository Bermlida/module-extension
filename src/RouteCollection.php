<?php

namespace Vista\Router;

use ArrayIterator;
use Vista\Router\Interfaces\RouteInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Vista\Router\Traits\RouteCollectionTrait;

class RouteCollection implements RouteCollectionInterface
{
    use RouteCollectionTrait;
    
    /**
     * The routes contained in the collection.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Set the route for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
/*
        if (is_string($offset) && $offset !== '') {
            $implements = class_implements($value);
            if (in_array(RouteInterface::class, $implements)) {
                $value->name($offset);
            }
        }
*/
        $this->setRoute($value);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getRoute($offset));
    }

    /**
     * Unset the route for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        return $this->removeRoute($offset);
    }

    /**
     * Get the route for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getRoute($offset);
    }

    /**
     * Get an iterator for the routes.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }

    /**
     * Count the number of routes in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->routes);
    }
}
