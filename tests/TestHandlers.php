<?php

class TestHandlerA
{
    public function __invoke(string $item_name)
    {
        return [
            'method' => __METHOD__,
            'name' => 'item_name',
            'value' => $item_name
        ];
    }
}

class TestHandlerB
{
    public function __invoke(string $item_prototype)
    {
        return [
            'method' => __METHOD__,
            'name' => 'item_prototype',
            'value' => $item_prototype
        ];
    }
}

class TestHandlerC
{
    public function process($user_id)
    {
        return [
            'method' => __METHOD__,
            'name' => 'user_id',
            'value' => $user_id
        ];
    }

    public function processWithModel(TestRouteModel $model)
    {
        return [
            'method' => __METHOD__,
            'name' => 'model',
            'value' => $model
        ];
    }
}

class TestHandlerD
{
    public function process($sort, $top)
    {
        return [
            'method' => __METHOD__,
            'name' => 'sort_top',
            'value' => ['sort' => $sort, 'top' => $top]
        ];
    }

    public function processWithModel(TestRouteModel $model)
    {
        return [
            'method' => __METHOD__,
            'name' => 'model',
            'value' => $model
        ];
    }
}
