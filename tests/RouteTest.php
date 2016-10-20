<?php

use Vista\Router\Route;
use Vista\Router\Tests\Modules\TestHandler;
use Vista\Router\Tests\Modules\TestRouteModel;
use Vista\Router\Tests\Modules\TestParamHandler;
use Phly\Http\ServerRequest;

/**
 * @coversDefaultClass \Vista\Router\Tests
 */
class RouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @codeCoverageIgnore
     */
    public function handlerProvider()
    {
        return [
            'anonymous with params' => [
                function ($user, $item_name, $item_property, $sort, $top) {
                    return [
                        'user_id' => $user->user_id,
                        'item_name' => $item_name,
                        'item_property' => $item_property,
                        'sort' => $sort,
                        'top' => $top
                    ];
                }
            ],
            'anonymous with route model' => [
                function (TestRouteModel $model) {
                    return $model->get_all_data();
                }
            ],
            'class name' => [[TestHandler::class]],
            'object' => [[new TestHandler()]],
            'class and method with params' => [[TestHandler::class, 'process']],
            'class and method with route model' => [[TestHandler::class, 'processWithModel']],
            'object and method with params' => [[new TestHandler(), 'process']],
            'object and method with route model' => [[new TestHandler(), 'processWithModel']]
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

        $this->assertEquals($route->path_prefix, 'users/{user}/account');
        $this->assertEquals($route->path, 'profiles/{item_name}/{item_property}');
        $this->assertEquals($route->full_path, 'users/{user}/account/profiles/{item_name}/{item_property}');

        $this->assertEquals($route->tokens, ['user' => '\d+', 'item_property' => 'name|value']);
        $this->assertEquals($route->full_regex, 'users\/(\d+)\/account\/profiles\/(\w+)\/(name|value)');

        $this->assertEquals($route->methods, ['get', 'options', 'header']);

        $this->assertEquals(
            $route->param_sources,
            [
                'user' => 'uri',
                'item_name' => 'uri',
                'item_property' => 'uri',
                'sort' => 'get',
                'top' => 'post'
            ]
        );
    }

    /**
     * @depends testRoute
     */
    public function testMatchUri(Route $route)
    {
        $request = $this->getRequest();

        $this->assertEquals($route->matchUri($request), true);
    }

    /**
     * @depends testRoute
     */
    public function testMatchMethod(Route $route)
    {
        $request = $this->getRequest();

        $this->assertEquals($route->matchMethod($request), true);
    }

    /**
     * @dataProvider handlerProvider
     * @depends testRoute
     */
    public function testExecuteHandler($handler, Route $route)
    {
        $request = $this->getRequest();
        
        $route->handler($handler);
        
        $this->assertEquals(
            $route->executeHandler($request),
            [
                'user_id' => 55,
                'item_name' => 'picture',
                'item_property' => 'name',
                'sort' => 220,
                'top' => 3.3
            ]
        );
    }

    protected function setRouteProperty(Route $route)
    {
        $route
            ->name_prefix('users.account.')->name('.profiles.item')
            ->path_prefix('/users/{user}/account/')->path('/profiles/{item_name}/{item_property}')
            ->tokens('user', '\d+')->tokens(['item_property' => 'name|value'])
            ->methods('get')->methods(['options', 'header']);
    }

    protected function setRouteParams(Route $route)
    {
        $route
            ->param_sources([
                    'user' => 'uri',
                    'item_name' => 'uri',
                    'item_property' => 'uri',
                    'sort' => 'get',
                    'top' => 'post'
                ])
            ->param_handlers([
                    'item_name' => [TestParamHandler::class],
                    'item_property' => [new TestParamHandler()]
                ])
            ->param_handlers('sort', [new TestParamHandler(), 'processTimesTen'])
            ->param_handlers('top', [TestParamHandler::class, 'processDividedTen'])
            ->param_handlers('user', function ($param) {
                    return (object)(['user_id' => $param]);
                });
    }

    protected function getRequest()
    {
        $request_params = [
            'REQUEST_URI' => '/users/55/account/profiles/picture/name',
            'REQUEST_METHOD' => 'get'
        ];

        return (new ServerRequest($request_params))
                        ->withQueryParams(['sort' => 22])
                        ->withParsedBody(['top' => 33]);
    }
}