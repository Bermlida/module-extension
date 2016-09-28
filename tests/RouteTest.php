<?php

use Vista\Router\Interfaces\RouteInterface;
use Vista\Router\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function routeProvider()
    {
        return [
            [new Route()]
        ];
    }

    /**
     * @dataProvider routeProvider
     */
    public function testSetRoute(RouteInterface $route)
    {
        $route
            ->name_prefix('users.account.')->name('.profiles.item')
            ->path_prefix('/users/{user_id}/account/')->path('/pofiles/{item_name}/{item_prototype}')
            ->tokens('user_id', '\d+')->tokens(['item_prototype' => 'name|value'])
            ->methods('get')->methods(['options', 'header']);

        return $route;
    }

    /**
     * @depends testName
     */
    public function testPath()
    {
        $route->path_prefix('/users/')->path('{user_id}/account/{profiles_item}');

        $this->assertEquals($this->name_prefix, 'users');
        $this->assertEquals($this->name, 'account.profiles');
        $this->assertEquals($this->full_name, 'users.account.profiles');

        $this->assertEquals($this->path_prefix, 'users');
        $this->assertEquals($this->path, '{user_id}/account/{profiles_item}');
        $this->assertEquals($this->full_path, 'users/{user_id}/account/{profiles_item}');

        return $route;
    }

    /**
     * @depends testPath
     */
    public function testRegex()
    {
        $route->tokens('user_id', '\d+');

        $this->assertEquals($this->full_regex, "users/(\d+)/account/('\w+)");

        return $route;
    }

    /**
     * @depends testMethod
     */
    public function testMethod()
    {
        $route

        $this->assertEquals($this->methods, ["GET", "OPTIONS", "HEADER"]);

        return $route;
    }

    /**
     * @depends testMethod
     */
    public function testMethod()
    {
        $route->methods('get')->methods(['options', 'header']);

        $this->assertEquals($this->methods, ["GET", "OPTIONS", "HEADER"]);

        return $route;
    }
}