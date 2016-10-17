<?php

use Vista\Router\RouteCollection;
use Vista\Router\RouteDispatcher;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $router = new Router(
            new RouteCollection(),
            new RouteDispatcher()
        );

        $this->router = $router;
    }

    public function testSetNameSpace()
    {
        $this->router->setNameSpace('');
    }

    public function testSetCustomSetting()
    {
        $this->router->setCustomSetting('');
    }

    public function testDefault()
    {
        $this->router->default(
            function (Router $router) {

            }
        );
    }

    public function testGroup()
    {
        $this->router->group(
            function (Router $router) {

            }
        );
    }
}