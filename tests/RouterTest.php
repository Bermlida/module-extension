<?php

use Vista\Router\Router;
use Vista\Router\RouteCollection;
use Vista\Router\RouteDispatcher;
use Vista\Router\Tests\Modules\TestHandler;

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
                            $user['user_id'] = $param;
                            return (object)$user;
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
                    ['get', 'header'],
                    function ($user, $setting_item, $setting_value) {
                        $user->$setting_item = $setting_value;
                    }
                )->param_sources(['setting_item' => 'uri', 'setting_value' => 'uri']);
            },
            'user'
        );
    }

    /**
     * @depends clone testDefault
     */
    public function testNonGroup(Route $router)
    {
        $router->options('/profiles', function () {
            return ['head', 'get', 'put', 'delete', 'post', 'patch'];
        });

        $router->head('/profiles/{profiles_item}')
        $router->get('/profiles/{profiles_item}')
        $router->delete('/profiles/{profiles_item}')

        $router
            ->put('/profiles/{item_name}',[TestHandler::class, 'process'])
            ->param_sources([
                'item_name' => 'uri',
                'item_property' => 'post',
                'sort' => 'get',
                'top' => 'get'
            ]);

        $router
            ->post('/profiles', [TestHandler::class])
            ->param_sources([
                'item_name' => 'post',
                'item_property' => 'post',
                'sort' => 'get',
                'top' => 'get'
            ]);

        $router
            ->patch('/profiles', [new TestHandler()])
            ->param_sources([
                'item_name' => 'post',
                'item_property' => 'post',
                'sort' => 'get',
                'top' => 'get'
            ]);
    }
}