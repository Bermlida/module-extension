<?php

class RouteTest extends PHPUnit_Framework_TestCase
{
    public function testFunc1()
    {
        $this->assertEquals(1, 2);
    }
    /**
     * @depends testFunc1
     * [testFunc2 description]
     * @return [type] [description]
     */
    public function testFunc2()
    {
        $this->assertEquals(2, 2);
    }
}