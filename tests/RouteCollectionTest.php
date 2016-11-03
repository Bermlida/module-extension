<?php

use Vista\Router\Route;
use Vista\Router\RouteCollection;

/**
 * @coversDefaultClass \Vista\Router\Tests
 */
class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    private $route;

    public function setUp()
    {
        $this->route = new Route();
    }

    public function testOffsetSet()
    {
        $collection = new RouteCollection();
        
        $collection[] = $this->route;
        
        $this->assertEquals($collection[0], $this->route);
        
        return $collection;
    }

    /**
     * @depends clone testOffsetSet
     */
    public function testOffsetExists(RouteCollection $collection)
    {
        $this->assertEquals(isset($collection[0]), true);

        $this->assertEquals(isset($collection[1]), false);
    }

    /**
     * @depends clone testOffsetSet
     */
    public function testOffsetGet(RouteCollection $collection)
    {
        $this->assertEquals($collection[0], $this->route);

        $this->assertEquals($collection[1], null);
    }

    /**
     * @depends clone testOffsetSet
     * @expectedException RuntimeException
     * @codeCoverageIgnore
     */
    public function testOffsetUnset(RouteCollection $collection)
    {
        unset($collection[0]);

        unset($collection[1]);
    }

    /**
     * @depends clone testOffsetSet
     */
    public function testGetIterator(RouteCollection $collection)
    {
        $this->assertInstanceOf(ArrayIterator::class, $collection->getIterator());
    }

    /**
     * @depends clone testOffsetSet
     */
    public function testCount(RouteCollection $collection)
    {
        $this->assertEquals(count($collection), 1);
    }
}