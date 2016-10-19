<?php

use Vista\Router\Traits\RouteSetterTrait;

class RouteSetterTraitTest extends PHPUnit_Framework_TestCase
{
    public function nameProvider()
    {
        return [
            'name1' => ['user.profiles.'],
            'name2' => ['.user.settings'],
            'name3' => ['.user.transaction.items.']
        ];
    }       
  
    public function pathProvider()
    {
        return [
            'path1' => ['{user_id}/profiles/all/'],
            'path2' => ['/{user_id}/settings/{setting_item}/{setting_value}'],
            'path3' => ['/{user_id}/transaction/{transaction_id}/items/']
        ];
    }
    
    public function tokensProvider()
    {
        return [
            'tokens1' => [['user_id' => '\d+']],
            'tokens2' => [['user_id' => '\d+', 'setting_item' => 'email|phone', 'setting_value' => '\w+|\S+']],
            'tokens3' => [['user_id' => '\d+', 'transaction_id' => '\d+']]
        ];
    }

    public function methodsProvider()
    {
        return [
            'methods1' => [['get', 'header']],
            'methods2' => [['put', 'delete', 'options']],
            'methods3' => [['post', 'patch']]
        ];
    }
    
    public function handlerProvider()
    {
        return [
            'handler1' => [['TestHandler']],
            'handler2' => [[new TestHandler()]],
            'handler3' => [['TestHandler', 'process']],
            'handler4' => [[new TestHandler(), 'process']],
            'handler5' => [
                function ($argument) {
                    return var_export($argument , true);
                }
            ]
        ];
    }
    
    public function paramSourcesProvider()
    {
        return [
            'param_sources1' => [[
                'user_id' => 'get'
            ]],
            'param_sources2' => [[
                'setting_item' => 'get',
                'setting_value' => 'post'
            ]],
            'param_sources3' => [[
                'sort_by' => 'post',
                'transaction' => 'files'
            ]]
        ];
    }
    
    public function paramHandlersProvider()
    {
        return [
            'param_handlers1' => [[
                'user_id' => function ($user_id) {
                    return (object)$user_id;
                }
            ]], 
            'param_handlers2' => [[
                'setting_item' => ['TestParamHandler'],
                'setting_value' => [new TestParamHandler()]
            ]],
            'param_handlers3' => [[
                'transaction' => ['TestParamHandler', 'process'],
                'sort_by' => [new TestParamHandler(), 'process']
            ]]
        ];
    }

    public function testStub()
    {
        $stub = $this->getMockForTrait(RouteSetterTrait::class);
        
        $stub->method('judgeValidRegex')->will(
            $this->returnCallback(
                function (string $regex) {
                    return !is_numeric($regex);
                }
            )
        );
        
        $stub->method('judgeValidMethod')->will(
            $this->returnCallback(
                function (string $method) {
                    return in_array($method, ['options', 'header', 'get', 'put', 'delete', 'patch', 'post']);
                }
            )
        );
        
        $stub->method('judgeValidHandler')->will(
            $this->returnCallback(
                function ($handler) {
                    if (is_array($handler)) {
                        if (is_object($handler[0]) || is_string($handler[0])) {
                            if (!isset($handler[1])|| is_string($handler[1])) {
                                return true;
                            }
                        }
                    } elseif (is_callable($handler)) {
                        return true;
                    }
                    return false;
                }
            )
        );

        $stub->method('judgeValidSource')->will(
            $this->returnCallback(
                function (string $source) {
                    return !empty($source) && !is_numeric($source);
                }
            )
        );

        return $stub;
    }

    /**
     * @dataProvider nameProvider
     * @depends testStub
     */
    public function testNamePrefix(string $name_prefix, $stub)
    {
        $stub_name_prefix = $stub->name_prefix($name_prefix)->name_prefix;

        $name_prefix = trim($name_prefix, '.');

        $this->assertEquals($stub_name_prefix, $name_prefix);
    }

    /**
     * @dataProvider pathProvider
     * @depends testStub
     */
    public function testPathPrefix(string $path_prefix, $stub)
    {
        $stub_path_prefix = $stub->path_prefix($path_prefix)->path_prefix;

        $path_prefix = trim($path_prefix, '/');

        $this->assertEquals($stub_path_prefix, $path_prefix);
    }

    /**
     * @dataProvider nameProvider
     * @depends testStub
     */
    public function testName(string $name, $stub)
    {
        $stub_name = $stub->name($name)->name;

        $name = trim($name, '.');

        $this->assertEquals($stub_name, $name);
    }

    /**
     * @dataProvider pathProvider
     * @depends testStub
     */
    public function testPath(string $path , $stub)
    {
        $stub_path = $stub->path($path)->path;

        $path = trim($path,  '/');
        
        $this->assertEquals($stub_path, $path);
    }

    /**
     * @dataProvider tokensProvider
     * @depends testStub
     */
    public function testTokens(array $tokens, $stub)
    {
        $stub->tokens = [];

        $stub_tokens = $stub->tokens($tokens)->tokens;

        $this->assertEquals($stub_tokens, $tokens);
    }

    /**
     * @dataProvider methodsProvider
     * @depends testStub
     */
    public function testMethods(array $methods, $stub)
    {
       $stub->methods  = [];

       $stub_methods = $stub->methods($methods)->methods;

       $this->assertEquals($stub_methods, $methods);
    }

    /**
     * @dataProvider handlerProvider
     * @depends testStub
     */
    public function testHandler($handler, $stub)
    {
        $stub_handler = $stub->handler($handler)->handler;

        $this->assertEquals($stub_handler, $handler);
    }
    
    /**
     * @dataProvider paramSourcesProvider
     * @depends testStub
     */
    public function testParamSources(array $param_sources, $stub)
    {
        $stub->param_sources = [];

        $stub_param_sources = $stub->param_sources($param_sources)->param_sources;

        $this->assertEquals($stub_param_sources, $param_sources);
    }

    /**
     * @dataProvider paramHandlersProvider
     * @depends testStub
     */
    public function testParamHandlers(array $param_handlers, $stub)
    {
        $stub->param_handlers = [];
        
        $stub_param_handlers = $stub->param_handlers($param_handlers)->param_handlers;
        
        $this->assertEquals($stub_param_handlers, $param_handlers);
    }
}