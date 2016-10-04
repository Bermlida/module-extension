<?php

class TestHandlerA
{
    public function __invoke(string $item_name)
    {
        var_dump('in TestHandlerA with item_name value: ' . $item_name);
    }
}

class TestHandlerB
{
    public function __invoke(string $item_prototype)
    {
        var_dump('in TestHandlerA with item_prototype value: ' . $item_prototype);
    }
}

class TestHandlerC
{
    public function process($user_id)
    {
        var_dump('in TestHandlerC::processWithModel with user_id value: ');
        var_dump($user_id);
    }

    public function processWithModel(TestRouteModel $model)
    {
        var_dump('in TestHandlerC::processWithModel with model: ');
        var_dump($model);
    }
}

class TestHandlerD
{
    public function process(string $item_name, string $item_prototype)
    {
        var_dump('in TestHandlerD::processWithModel with item_name and item_prototype');
        var_dump('item_name: ' . $item_name);
        var_dump('item_prototype' . $item_prototype);
    }

    public function processWithModel(TestRouteModel $model)
    {
        var_dump('in TestHandlerD::processWithModel with model: ');
        var_dump($model);
    }
}
