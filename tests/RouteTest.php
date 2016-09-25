<?php

use Vista\Router\Interfaces\RouteInterface;
use Vista\Router\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function additionProvider()
    {
        return [
            [new Route()]
        ];
    }

    public function testName(RouteInterface $route)
    {
        $route->name_prefix("user")->name("accounts");

        $this->assertEquals($this->name_prefix, "user");
        $this->assertEquals($this->name, "accounts");
        $this->assertEquals($this->full_name, "user.accounts");
    }

    public function testPath()
    {
        $this->assertEquals(2, 2);
    }
}