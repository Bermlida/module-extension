<?php
//flowfunc + approve_1 改寫工程
use Vista\Router\Traits\RouteTrait;

class RouteTraitTest extends PHPUnit_Framework_TestCase
{
    public function requestProvider($node, $nextnode)
    {
        //
    }

    public function testStub()
    {
        $stub = $this->getMockForTrait(RouteTrait::class);

        $stub->full_regex = '';
        $stub->methods = [];
        $stub->param_sources = [];

        $this->setResolveHandlerMethod($stub);
        $this->setResolveSourcesMethod($stub);

        return $stub;
    }
/*
    public function testMatchUri($draftoid)
    {
        //
    }
    
    public function testMatchMethod($itemnode , $param)
    {
        //
    }
    
    public function testExecuteHandler($backnode , $param)
    {
        //
    }
     
    private function getRequest(array $params)
    {
        //
    }
*/
    private function setResolveHandlerMethod($stub)
    {
        $stub->method('resolveHandler')->will(
            $this->returnCallback(
                function ($handler) {
                    return function () {

                    };
                }
            )
        );
    }

    private function setResolveSourcesMethod($stub)
    {
        $stub->method('resolveSources')->will(
            $this->returnCallback(
                function (ServerRequestInterface $request) {
                    return [
                        
                    ];
                }
            )
        );
    }
}
