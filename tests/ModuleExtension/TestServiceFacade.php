<?php

namespace App\Services;

class TestServiceFacade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor() 
    { 
        return 'TEST'; 
    }
}