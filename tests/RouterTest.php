<?php

use Phly\Http\ServerRequest;
use Vista\Router\Router;
use Vista\Router\RouteCollection;
use Vista\Router\RouteDispatcher;
use Vista\Router\Tests\Modules\TestHandler;
use Vista\Router\Tests\Modules\TestParamHandler;

/**
 * @coversDefaultClass \Vista\Router\Tests
 */
class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testRouter()
    {
        $router = new Router(
            new RouteCollection(),
            new RouteDispatcher()
        );

        return $router;
    }

    /**
     * @depends clone testRouter
     */
    public function testSetRootNamespace(Router $router)
    {
        $router->setRootNamespace('Vista\Router\Tests\Modules');

        return $router;
    }

    /**
     * @depends clone testRouter
     */
    public function testSetCustomSetting(Router $router)
    {
        $router->setCustomSetting('test_custom_setting');

        return $router;
    }

    /**
     * @depends clone testRouter
     */
    public function testDefault(Router $router)
    {
        $router->default()->tokens(['user' => '\d+']);

        $router->default(
            function (Router $router) {
                $router
                    ->param_sources(['user' => 'uri'])
                    ->param_handlers('user', function ($param) {
                        return (object)['user_id' => $param];
                    });
            }
        );

        return $router;
    }

    /**
     * @depends clone testDefault
     */
    public function testGroup(Router $router)
    {
        $router->group(
            'users/{user}/',
            function (Router $router) {
                $router->route(
                    '/account/{setting_item}/{setting_value}',
                    ['get', 'header', 'put'],
                    function ($user, $setting_item, $setting_value) {
                        $user->$setting_item = $setting_value;
                        return (array)$user;
                    }
                )->tokens('setting_value', '\w+\.\w+')->param_sources([
                    'setting_item' => 'uri',
                    'setting_value' => 'uri'
                ]);
            },
            'user'
        );

        return $router;
    }

    /**
     * @depends clone testDefault
     */
    public function testNonGroup(Router $router)
    {
        $router->default()
            ->path_prefix('users/{user}/')
            ->param_sources([
                'user' => 'uri',
                'item_name' => 'uri',
                'item_property' => 'post',
                'sort' => 'get',
                'top' => 'get'
            ]);

        $router->options('/profiles', function () {
            return ['head', 'get', 'put', 'delete', 'post', 'patch'];
        });

        $router->head('/profiles/{item_name}', [new TestHandler(), 'processWithModel']);

        $router->delete('/profiles/{item_name}', [TestHandler::class, 'processWithModel']);

        $router->get('/profiles/{item_name}', [new TestHandler(), 'process']);
        
        $router->put('/profiles/{item_name}',[TestHandler::class, 'process']);
            
        $router->post('/profiles', [TestHandler::class])->param_sources('item_name', 'post');

        $router->patch('/profiles', [new TestHandler()])->param_sources('item_name', 'post');

        return $router;
    }

    /**
     * @depends testSetRootNamespace
     * @depends testSetCustomSetting
     * @depends testGroup
     * @depends testNonGroup
     */
    public function testDispatch($default, $custom, $rule_group, $rule_no_group)
    {
        $this->assertEquals(
            $default->dispatch(
                (new ServerRequest([
                    'REQUEST_URI' => '/test_default_handler/process_with_request',
                    'REQUEST_METHOD' => 'patch'
                ]))->withQueryParams(['sort' => 22])->withParsedBody(['top' => 33])
            ),
            ['sort' => 22, 'top' => 33]
        );

        // $this->assertEquals($custom->dispatch($request));

        $this->assertEquals(
            $rule_group->dispatch(
                new ServerRequest([
                    'REQUEST_URI' => 'users/55/account/picture/photo.jpg',
                    'REQUEST_METHOD' => 'put'
                ])
            ),
            ['user_id' => 55, 'picture' => 'photo.jpg']
        );

        $this->assertEquals(
            ($options = $rule_no_group->dispatch(
                (new ServerRequest(['REQUEST_URI' => 'users/55/profiles','REQUEST_METHOD' => 'options']))
            )),
            ['head', 'get', 'put', 'delete', 'post', 'patch']
        );

        foreach ($options as $method) {
            $uri = $method == 'post' || $method == 'patch' ? 'users/55/profiles/' : 'users/55/profiles/test_item';
            $request = (new ServerRequest(['REQUEST_URI' => $uri, 'REQUEST_METHOD' => $method]))
                                    ->withQueryParams(['sort' => 22, 'top' => 33])
                                    ->withParsedBody(['item_name' => 'test_item', 'item_property' => 'test_value']);
            
            $this->assertEquals($rule_no_group->dispatch($request), [
                'user_id' => 55,
                'item_name' => 'test_item',
                'item_property' => 'test_value',
                'sort' => 22,
                'top' => 33
            ]);
        }
    }
}