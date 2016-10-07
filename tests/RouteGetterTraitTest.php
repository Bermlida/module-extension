<?php

use Vista\Router\Traits\RouteGetterTrait;

class RouteGetterTraitTest extends PHPUnit_Framework_TestCase
{
    public function handlerProvider()
    {
        return [
            'handler1' => [['TestHandlerA']],
            'handler2' => [[new TestHandlerB()]],
            'handler3' => [['TestHandlerC', 'process']],
            'handler4' => [[new TestHandlerD(), 'process']],
            'handler5' => [
                function ($argument) {
                    return var_export($argument , true);
                }
            ]
        ];
    }
    
    public function testStub()
    {
        $stub = $this->getMockForTrait(RouteGetterTrait::class);
        
        $stub->name_prefix = 'users';
        $stub->path_prefix = 'users/{user_id}';
        $stub->name = 'profiles.brief';
        $stub->path = 'profiles/brief';

        $stub->tokens = ['user_id' => '\d+'];
        $stub->methods = ['get', 'put', 'post', 'patch'];
        $stub->param_sources = ['post', 'file', 'uri'];
        
        return $stub;
    }
    
    /**
     * @depends testStub
     */
    public function testNamePrefix($stub)
    {
        $this->assertEquals($stub->__get('name_prefix'), 'users');
    }

    /**
     * @depends testStub
     */
    public function testPathPrefix($stub)
    {
        $this->assertEquals($stub->__get('path_prefix'), 'users/{user_id}');
    }
    
    /**
     * @depends testStub
     */
    public function testName($stub)
    {
        $this->assertEquals($stub->__get('name'), 'profiles.brief');
    }
    
    /**
     * @depends testStub
     */
    public function testPath($stub)
    {
        $this->assertEquals($stub->__get('path'), 'profiles/brief');
    }

    /**
     * @depends testStub
     */
    public function testTokens($stub)
    {
        $this->assertEquals($stub->__get('tokens'), ['user_id' => '\d+']);
    }

    /**
     * @depends testStub
     */
    public function testFullName($stub)
    {
        $this->assertEquals($stub->__get('full_name'), 'users.profiles.brief');
    }

    /**
     * @depends testStub
     */
    public function testFullPath($stub)
    {
        $this->assertEquals($stub->__get('full_path'), 'users/{user_id}/profiles/brief');
    }

    /**
     * @depends testStub
     */
    public function testFullRegex($stub)
    {
        $this->assertEquals($stub->__get('full_regex'), 'users\/(\d+)\/profiles\/brief');
    }

    /**
     * @depends testStub
     */
    public function testMethods($stub)
    {
        $this->assertEquals($stub->__get('methods'), ['get', 'put', 'post', 'patch']);
    }

    /**
     * @dataProvider handlerProvider
     * @depends testStub
     */
    public function testHandler($handler,  $stub)
    {
        $stub->handler = $handler;

        $this->assertEquals($stub->__get('handler'), $handler);
        
        // $this->assertTrue(is_callable($stub->__get('handler_resolve')));
    }

    /**
     * @depends testStub
     */
    public function testParamSources($stub)
    {
        $this->assertEquals($stub->__get('param_sources'), ['post', 'file', 'uri']);
    }

    /**
     * @dataProvider handlerProvider
     * @depends testStub
     */
    public function testParamHandlers($handler, $stub)
    {
        $stub->param_handlers = ['user_id' => $handler];

        $this->assertEquals($stub->__get('param_handlers'), ['user_id' => $handler]);

        // $this->assertFalse(in_array(
        //     false,
        //     array_map(
        //         'is_callable',
        //         $stub->__get('param_handlers_resolve')
        //     )
        // ));
    }
}
