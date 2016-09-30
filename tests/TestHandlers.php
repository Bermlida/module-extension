<?php

class TestHandlerA
{
    public function __invoke($param)
    {

    }
}

class TestHandlerB
{
    public function __invoke($param)
    {
    }
}

class TestHandlerC
{
    public function process($param)
    {
    }

    public function processWithModel(TestRouteModel $model)
    {
        var_dump($model);
    }
}

class TestHandlerD
{
    public function process($param)
    {
        print "<pre>";
    }

    public function processWithModel(TestRouteModel $model)
    {
        var_dump($model);
    }
}
