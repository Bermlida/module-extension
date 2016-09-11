<?php

namespace Vista\Router\Traits;

use RuntimeException;

trait RouteCollectionTrait
{
    public function getIterator()
    {
        return new ArrayIterator($this->routes);
    }

    public function count() 
        if (method_exists($this, "countRoutes")) {
            return $this->countRoutes();
        } else {
            count($this->routes);
        }
    } 
}

/* End of file UserSetting.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/Entities/UserSetting.php */
