<?php

class TestParamHandlerA
{
    public function __invoke($param)
    {
        return $param;
    }
}

class TestParamHandlerB
{
    public function __invoke($param)
    {
        return $param;
    }
}

class TestParamHandlerC
{
    public function process($param)
    {
        return $param;
    }
}

class TestParamHandlerD
{
    public function process($param)
    {
        return $param;
    }
}
/*
 implements ArrayAccess
private $container = array();
$obj = new obj();
$obj['key'] = new abc();
use Vista\Router\Interfaces\RouteModelInterface;
*/