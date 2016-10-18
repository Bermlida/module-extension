<?php

use Vista\Router\Route;
use Phly\Http\ServerRequest;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function requestProvider()
    {
        return array_map([$this, 'getRequest'], [
            [
                'uri' => '/users/55/account/profiles/picture/name',
                'method' => 'get',
                'query' => ['sort' => 22],
                'parsed_body' => ['top' => 33]
            ]
        ]);
    }

    public function testHandlers()
    {
        return [
            function ($sort) {
                return [
                    'method' => 'anonymous',
                    'name' => 'sort',
                    'value' => $sort
                ];
            },
            function (TestRouteModel $model) {
                return [
                    'method' => 'anonymous with model',
                    'name' => 'model',
                    'value' => $model
                ];
            },
            ['TestHandlerA'],
            [new TestHandlerB()],
            ['TestHandlerC', 'process'],
            ['TestHandlerC', 'processWithModel'],
            [new TestHandlerD(), 'process'],
            [new TestHandlerD(), 'processWithModel']
        ];
    }

    public function testRoute()
    {
        $route = new Route();
        
        $this->setRouteProperty($route);

        $this->setRouteParams($route);
        
        return $route;
    }
    
    /**
     * @depends testRoute
     */
    public function testGetter(Route $route)
    {
        $this->assertEquals($route->name_prefix, 'users.account');
        $this->assertEquals($route->name, 'profiles.item');
        $this->assertEquals($route->full_name, 'users.account.profiles.item');

        $this->assertEquals($route->path_prefix, 'users/{user_id}/account');
        $this->assertEquals($route->path, 'profiles/{item_name}/{item_property}');
        $this->assertEquals($route->full_path, 'users/{user_id}/account/profiles/{item_name}/{item_property}');

        $this->assertEquals($route->tokens, ['user_id' => '\d+', 'item_property' => 'name|value']);
        $this->assertEquals($route->full_regex, 'users\/(\d+)\/account\/profiles\/(\w+)\/(name|value)');

        $this->assertEquals($route->methods, ['get', 'options', 'header']);

        $this->assertEquals($route->param_sources, ['uri', 'get', 'post', 'file']);
    }

    /**
     * @dataProvider requestProvider
     * @depends testRoute
     */
    public function testMatchUri(ServerRequest $request, Route $route)
    {
        $this->assertEquals($route->matchUri($request), true);
    }

    /**
     * @dataProvider requestProvider
     * @depends testRoute
     */
    public function testMatchMethod(ServerRequest $request, Route $route)
    {
        $this->assertEquals($route->matchMethod($request), true);
    }

    /**
     * @dataProvider requestProvider
     * @depends testRoute
     * @depends testHandlers
     */
    public function testExecuteHandler(ServerRequest $request, Route $route, array $handlers)
    {
        foreach ($handlers as $handler) {
            $route->handler($handler);
        
            var_dump($route->executeHandler($request));
        }
    }

    protected function setRouteProperty(Route $route)
    {
        $route
            ->name_prefix('users.account.')->name('.profiles.item')
            ->path_prefix('/users/{user_id}/account/')->path('/profiles/{item_name}/{item_property}')
            ->tokens('user_id', '\d+')->tokens(['item_property' => 'name|value'])
            ->methods('get')->methods(['options', 'header']);
    }

    protected function setRouteParams(Route $route)
    {
        $route
            ->param_sources([
                    'sort' => 'get',
                    'top' => 'post'
                ])
            ->param_sources('user_id', 'uri')
            ->param_sources('item_name', 'uri')
            ->param_sources('item_property', 'uri')
            ->param_handlers([
                    'item_name' => ['TestParamHandler'],
                    'item_property' => [new TestParamHandler()]
                ])
            ->param_handlers('sort', [new TestParamHandler(), 'processTimesTen'])
            ->param_handlers('top', ['TestParamHandler', 'processDividedTen'])
            ->param_handlers('user_id', function ($param) {
                    return (object)(['user_id' => $param]);
                });
    }

    protected function getRequest(array $params)
    {
        $request_params = [
            'REQUEST_URI' => $params['uri'],
            'REQUEST_METHOD' => $params['method']
        ];

        $request = (new ServerRequest($request_params))
                                ->withQueryParams(($params['query'] ?? []))
                                ->withParsedBody(($params['parsed_body'] ?? []))
                                ->withUploadedFiles(($params['uploaded_files'] ?? []));
        return [$request];
    }
}