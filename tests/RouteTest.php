<?php

use Vista\Router\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    // public function handlerProvider()
    // {
    //     return [
    //         [
    //             function () {
    //                 print 'test';
    //             }
    //         ],
    //         [[$this]],
    //         [['stdClass']],
    //         [['stdClass', 'test']],
    //         [[$this, 'test']]
    //     ];
    // }
/*
*/
    public function handlerProvider()
    {
        return [
            [
                function ($user_id) {
                    print 'in anonymous with param';
                }
            ],
            [
                function (TestRouteModel $model) {
                    print 'in anonymous with param';
                    var_dump($model);
                }
            ],
            [['TestHandlerA']],
            [[new TestHandlerB()]],
            [['TestHandlerC', 'process']],
            [['TestHandlerC', 'processWithModel']],
            [[new TestHandlerD(), 'process']],
            [[new TestHandlerD(), 'processWithModel']]
        ];
    }

    /**
     * @dataProvider handlerProvider
     */
    public function testSetter($handler)
    {
        $route = new Route();
        
        $route
            ->name_prefix('users.account.')->name('.profiles.item')
            ->path_prefix('/users/{user_id}/account/')->path('/pofiles/{item_name}/{item_prototype}')
            ->tokens('user_id', '\d+')->tokens(['item_prototype' => 'name|value'])
            ->methods('get')->methods(['options', 'header'])
            ->handler($handler);

        $route
            ->param_sources('Uri')->param_sources(['get', 'post', 'file'])
            ->param_handlers([
                    'item_name' => ['TestParamHandlerA'],
                    'item_prototype' => [new TestParamHandlerB()]
                ])
            ->param_handlers('sort', [new TestParamHandlerC(), 'process'])
            ->param_handlers('top', ['TestParamHandlerD', 'process'])
            ->param_handlers('user_id', function ($param) {
                    return (object)$param;
                });

        return $route;
    }

    /**
    public function init()
    {
        $route = new Route();
        
        
        return $route;
    }
     */
    public function testGetter(Route $route)
    {
        $this->assertEquals($route->name_prefix, 'users.account');
        $this->assertEquals($route->name, 'profiles.item');
        $this->assertEquals($route->full_name, 'users.account.profiles.item');

        $this->assertEquals($route->path_prefix, 'users/{user_id}/account');
        $this->assertEquals($route->path, 'pofiles/{item_name}/{item_prototype}');
        $this->assertEquals($route->full_path, 'users/{user_id}/account/pofiles/{item_name}/{item_prototype}');

        $this->assertEquals($route->tokens, ['user_id' => '\d+', 'item_prototype' => 'name|value']);
        $this->assertEquals($route->full_regex, 'users\/(\d+)\/account\/pofiles\/(\w+)\/(name|value)');

        $this->assertEquals($route->methods, ['GET', 'OPTIONS', 'HEADER']);

        $this->assertEquals($route->param_sources, ['uri', 'get', 'post', 'file']);
    }

    /**
     * 
     * 
     */
    public function testSetHandler()
    {
        // $route->handler($handler);
        // $route->param_handlers()

        // $this->assertEquals($route->handler, $handler);
        // $this->assertEquals(is_callable($route->handler_resolve), true);

        // return $route;
        // $route
        // $route->methods('get')->methods(['options', 'header']);
            
            
        // $this->assertEquals($route)

        // return $route;

        // return $route;
    }
    

}