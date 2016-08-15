<?php

namespace App\Services;

use ReflectionMethod;

class TestServiceFacade extends \Illuminate\Support\Facades\Facade
{

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            $result = $this->$name();
            

        }
        print "message:::::" . $message . '<br>';
    }
}