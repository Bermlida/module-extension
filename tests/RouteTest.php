<?php

use Vista\Router\Route;
use Phly\Http\ServerRequest;

class RouteTest extends PHPUnit_Framework_TestCase
{
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
    
    public function requestProvider()
    {
        return [
            []
        ]
    }

    public function setRoutePrototype(Route $route, $handler)
    {
        $route
            ->name_prefix('users.account.')->name('.profiles.item')
            ->path_prefix('/users/{user_id}/account/')->path('/pofiles/{item_name}/{item_prototype}')
            ->tokens('user_id', '\d+')->tokens(['item_prototype' => 'name|value'])
            ->methods('get')->methods(['options', 'header'])
            ->handler($handler);
    }

    public function setRouteParams(Route $route)
    {
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
    }

    
    public function init($handler)
    {
        $route = new Route();
        
        $this->setRoutePrototype($route, $handler);

        $this->setRouteParams($route);
        
        return $route;
    }
    
    public function requestProvider()
    {
        $request = (new Request())
                                ->withUri(new Phly\Http\Uri('http://example.com'))
                                ->withMethod('PATCH')
                                ->withAddedHeader('Authorization', 'Bearer ' . $token)
                                ->withAddedHeader('Content-Type', 'application/json');

        return $request;
    }
    /**
     *  @dataProvider handlerProvider
     */
    public function testGetter($handler)
    {
        $route = $this->init($handler);

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
     * @dataProvider handlerProvider
     * 
     */
    public function testMatchUri($handler)
    {
        $route = $this->init($handler);

        $this->assertEquals($route->matchUri($request), true);
        // return;
        // $route->methods('get')->methods(['options', 'header']);
        // return 
        // return 
    }

    /**
     * @dataProvider handlerProvider
     * 
     */
    public function testMatchMethod()
    {
        $route = $this->init($handler);
        
        $this->assertEquals($route->matchMethod($request), true);
    }
    
    /**
     * @dataProvider handlerProvider
     * 
     */
    public function testExecuteHandler()
    {
        $route = $this->init($handler);
        
        $this->assertEquals($route->executeHandler($request), null);
    }
}