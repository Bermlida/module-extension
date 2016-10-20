<?php 

use Vista\Router\Traits\RouteGetterTrait;
use Vista\Router\Tests\Modules\TestHandler;
use Vista\Router\Tests\Modules\TestParamHandler;

/**
 * @coversDefaultClass \Vista\Router\Tests
 */
class RouteGetterTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @codeCoverageIgnore
     */
    public function handlerProvider()
    {
        return [
            'handler1' => [[TestHandler::class]],
            'handler2' => [[new TestHandler()]],
            'handler3' => [[TestHandler::class, 'process']],
            'handler4' => [[new TestHandler(), 'process']],
            'handler5' => [
                function ($argument) {
                    return var_export($argument , true);
                }
            ]
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function paramHandlerProvider()
    {
        return [
            'param_handler1' => [[TestParamHandler::class]],
            'param_handler2' => [[new TestParamHandler()]],
            'param_handler3' => [[TestParamHandler::class, 'process']],
            'param_handler4' => [[new TestParamHandler(), 'process']],
            'param_handler5' => [
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
        $stub->param_sources = ['user_id' => 'uri'];
        
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
        $this->assertEquals($stub->__get('param_sources'), ['user_id' => 'uri']);
    }

    /**
     * @dataProvider paramHandlerProvider
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
