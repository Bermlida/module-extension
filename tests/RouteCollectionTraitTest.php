<?php

use Vista\Router\Route;
use Vista\Router\Traits\RouteCollectionTrait;

/**
 * @coversDefaultClass \Vista\Router\Tests
 */
class RouteCollectionTraitTest extends PHPUnit_Framework_TestCase
{
    private $route;

    public function setUp()
    {
        $route = (new Route())
                            ->name_prefix('users.account.')->name('settings')
                            ->path_prefix('/users/{user_id}/account/')->path('/settings/{item}')
                            ->methods('get')->methods(['put', 'delete']);

        $this->route = $route;
    }

    public function testStub()
    {
        $stub = $this->getMockForTrait(RouteCollectionTrait::class);

        $stub->routes = [];

        return $stub;
    }

    /**
     * @depends clone testStub
     */
    public function testSetRoute($stub)
    {
        $stub->setRoute($this->route);

        return $stub;
    }

    /**
     * @depends clone testStub
     */
    public function testSetRoutes($stub)
    {
        $stub->setRoutes([$this->route]);

        return $stub;
    }

    /**
     * @depends testSetRoutes
     */
    public function testGetRoutes($stub)
    {
        $this->assertEquals($stub->getRoutes(), [$this->route]);
    }

    /**
     * @depends testSetRoutes
     */
    public function testRemoveRoutes($stub)
    {
        $stub->removeRoutes();

        $this->assertEquals($stub->getRoutes(), []);
    }

    /**
     * @depends testSetRoute
     */
    public function testGetRoute($stub)
    {
        $this->assertEquals($stub->getRoute(0), $this->route);

        $this->assertEquals(
            $stub->getRoute('/users/{user_id}/account/settings/{item}', 'put'),
            $this->route
        );

        $this->assertEquals(
            $stub->getRoute(
                'users/{user_id}/account/settings/{item}/',
                ['delete', 'put']
            ),
            $this->route
        );
    }

    /**
     * @depends testSetRoute
     */
    public function testRemoveRoute($stub)
    {
        (clone $stub)->removeRoute(0);

        (clone $stub)->removeRoute('/users/{user_id}/account/settings/{item}/', 'get');

        (clone $stub)->removeRoute('users/{user_id}/account/settings/{item}', ['get', 'delete']);
    }
    
    /**
     * @depends testSetRoute
     */
    public function testSearchRoute($stub)
    {
        $this->assertEquals($stub->searchRoute('users/{user_id}/account/settings/{item}/', ['delete', 'put']), 0);

        $this->assertEquals($stub->searchRoute('/users/{user_id}/account/settings/{item}', 'get'), 0);

        $this->assertEquals($stub->searchRoute('/users/{user_id}/account/settings/{item}/', 'options'), -1);

        $this->assertEquals($stub->searchRoute('users/{user_id}/account/settings/{item}', 'header'), -1);

        $this->assertEquals($stub->searchRoute('users/{user_id}/account/profiles', 'get'), -1);

        $this->assertEquals($stub->searchRoute('/users/{user_id}/account/profiles/', ['get', 'put', 'delete']), -1);
    }
}