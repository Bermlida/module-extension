<?php

namespace Vista\Router\Traits;

use RuntimeException;

trait RouteCollectionTrait
{
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }

    public function offsetSet($offset, $value)
    {
        if (is_string($offset) && !empty($offset) && is_a($value, "Route")) {
            $value->name($offset);

            if (method_exists($this, "setRoute")) {
                $this->setRoute($value);
            } else {
                $this->routes[$offset] = $value;
            }
        } else {
            throw new RuntimeException('');
        }
    }

    public function offsetExists($offset)
    {
        if (method_exists($this, "existsRoute")) {
            $this->existsRoute($offset);
        } else {
            return isset($this->routes[$offset]);
        }
    }

    public function offsetUnset($offset)
    {
        if (method_exists($this, "unsetRoute")) {
            $this->unsetRoute($offset);
        } else {
            if (isset($this->routes[$offset])) {
                unset($this->routes[$offset]);
            }
        }
    }

    public function offsetGet($offset)
    {
        if (method_exists($this, "getRoute")) {
            return $this->getRoute($offset);
        } else {
            return isset($this->routes[$offset]) ? $this->routes[$offset] : null;
        }
    } 

    public function count() 
    { 
        if (method_exists($this, "countRoutes")) {
            return $this->countRoutes();
        } else {
            count($this->routes);
        }
    } 
}

/* End of file UserSetting.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/Entities/UserSetting.php */
