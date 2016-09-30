<?php

class TestParamHandlerA
{
    public function __invoke($param)
    {
        
    }
}

class TestParamHandlerB
{
    public function __invoke($param)
    {
    }
}

class TestParamHandlerC
{
    public function process($param)
    {
    }
}

class TestParamHandlerD
{
    public function process($param)
    {
        print "<pre>";
    }
}
/*
 implements ArrayAccess
    private $container = array();
$obj = new obj();
$obj['key'] = new abc();
use Vista\Router\Interfaces\RouteModelInterface;
    
*/