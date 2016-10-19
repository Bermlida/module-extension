<?php

use Vista\Router\Route;
use Vista\Router\RouteCollection;
use Vista\Router\Prototypes\RouteDispatcherPrototype;
use Phly\Http\ServerRequest;

class RouteDispatcherPrototypeTest extends PHPUnit_Framework_TestCase
{
    public function testStub()
    {
        $stub = $this->getMockForAbstractClass(RouteDispatcherPrototype::class);

        return $stub;
    }

    /**
     * @depends clone testStub
     */
    public function testDefaultHandle($stub)
    {
        extract($this->getDefaultHandleParams());

        $stub->method('getClass')->willReturn('TestHandler');
        $stub->method('getMethod')->willReturn('process');
        $stub->method('bindArguments')->willReturn([$request]);

        $stub->default($param)->handle($request);
        
        $this->assertEquals($stub->executed(), true);
        $this->assertEquals($stub->result(), $request);
    }

    /**
     * @depends clone testStub
     */
    public function testRuleHandle($stub)
    {
        extract($this->getRuleHandleParams());

        $stub->rule($param)->handle($request);

        $this->assertEquals($stub->executed(), true);

        $this->assertEquals(
            $stub->result(),
            ['user_id' => 55, 'sort' => 220, 'top' => 3.3]
        );
    }

    private function getDefaultHandleParams()
    {
        $root_namespace = '\Vista\Router\Tests\Handlers\\';

        $request = $this->getRequest([
            'uri' => '/test_handler/process',
            'method' => 'get',
            'query' => ['var_get' => 123456],
            'parsed_body' => ['var_post' => 987654]
        ]);

        return [
            'param' => $root_namespace,
            'request' => $request
        ];
    }

    private function getRuleHandleParams()
    {
        $rules = new RouteCollection();
        $rules->setRoutes([
            (new Route())
                ->path('/users/{user_id}/profiles')
                ->methods(['get', 'header'])
                ->handler(function ($user_id, $sort, $top) {
                        return compact(['user_id', 'sort', 'top']);
                    })
                ->param_sources(['sort' => 'get', 'top' => 'post'])
                ->param_handlers('sort', ['TestParamHandler', 'processTimesTen'])
                ->param_handlers('top', [new TestParamHandler(), 'processDividedTen'])
        ]);

        $request = $this->getRequest([
            'uri' => '/users/55/profiles/',
            'method' => 'get',
            'query' => ['sort' => 22],
            'parsed_body' => ['top' => 33]
        ]);
        
        return ['param' => $rules, 'request' => $request];
    }

    private function getRequest(array $params)
    {
        $request_params = [
            'REQUEST_URI' => $params['uri'],
            'REQUEST_METHOD' => $params['method']
        ];

        $request = (new ServerRequest($request_params))
                                ->withQueryParams(($params['query'] ?? []))
                                ->withParsedBody(($params['parsed_body'] ?? []))
                                ->withUploadedFiles(($params['uploaded_files'] ?? []));

        return $request;
    }
}