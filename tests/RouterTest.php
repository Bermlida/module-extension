<?php

use Vista\Router\Router;
use Vista\Router\RouteCollection;
use Vista\Router\RouteDispatcher;

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
        $router->setRootNamespace('Vista\Router\Tests\Handlers');

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
                    ->param_sources(['uri'])
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
                );
            },
            'user'
        );
    }

    /**
     * @depends clone testDefault
     */
    public function testNonGroup(Route $router)
    {
        $router
            ->options('/profiles/{profiles_item}')
            ->head('/profiles/{profiles_item}')
            ->get('/profiles/{profiles_item}')
            ->put('/profiles/{profiles_item}/{profiles_value}')
            ->delete('/profiles/{profiles_item}')
            ->post('/profiles')
            ->patch('/profiles');
    }
}