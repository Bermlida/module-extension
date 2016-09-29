<?php

// use Vista\Router\Interfaces\RouteInterface;
use Vista\Router\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function handlerProvider()
    {
        return [
            [
                function () {
                    print 'test';
                }
            ],
            [[$this]],
            [['stdClass']],
            [['stdClass', 'test']],
            [[$this, 'test']]
        ];
    }

    public function testSetPrototype()
    {
        $route = new Route();

        $route
            ->name_prefix('users.account.')->name('.profiles.item')
            ->path_prefix('/users/{user_id}/account/')->path('/pofiles/{item_name}/{item_prototype}')
            ->tokens('user_id', '\d+')->tokens(['item_prototype' => 'name|value'])
            ->methods('get')->methods(['options', 'header'])
            ->param_sources('Uri')->param_sources(['get', 'post', 'file']);

        return $route;
    }

    /**
     * @depends testSetPrototype
     * @dataProvider handlerProvider
     */
    public function testSetHandler($handler, Route $route)
    {
        $route->handler($handler);

        $this->assertEquals($route->handler, $handler);

        return $route;
    }

    /**
     * @depends testSetPrototype
     */
    public function testEqualPrototype(Route $route)
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

    public function testMethod()
    {
        $route->path_prefix('/users/')->path('{user_id}/account/{profiles_item}');


        return $route;
    }

    public function testMethod2()
    {
        $route->methods('get')->methods(['options', 'header']);


        return $route;
    }
     */
}