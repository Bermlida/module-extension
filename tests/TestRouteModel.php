<?php

use Vista\Router\Interfaces\RouteModelInterface;

class TestRouteModel implements RouteModelInterface
{
    private $item_name;
    private $item_prototype;
    
    public function __construct(string $item_name, string $item_prototype)
    {
        $this->item_name = $item_name;
        $this->item_prototype = $item_prototype;
    }
}