<?php

class TestParamHandlerA
{
    public function __invoke($param)
    {
        return 'old_' . $param . '_list';
    }
}

class TestParamHandlerB
{
    public function __invoke($param)
    {
        return 'new_' . $param . '_collection';
    }
}

class TestParamHandlerC
{
    public function process($param)
    {
        return $param * 10;
    }
}

class TestParamHandlerD
{
    public function process($param)
    {
        return $param / 10;
    }
}
/*
 implements ArrayAccess
private $container = array();
$obj = new obj();
$obj['key'] = new abc();
use Vista\Router\Interfaces\RouteModelInterface;

        //'get'->methods(['options', 'header']);
*/