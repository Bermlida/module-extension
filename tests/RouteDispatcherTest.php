<?php

use Vista\Router\RouteDispatcher;

class RouteDispatcherTest extends PHPUnit_Framework_TestCase
{
    public function defaultRequestProvider()
    {
        return [
            'with route model' => [
                'params' => $this->getParamsMatchRouteModel(),
                'result' => $this->getResultMatchRouteModel()
            ],
            'with server request' => [
                'params' => $this->getParamsMatchServerRequest(),
                'result' => $this->getResultMatchServerRequest()
            ],
            'with params' => [
                'params' => $this->getParamsMatchParams(),
                'result' => $this->getResultMatchParams()
            ]
        ];
    }

    public function testDefault()
    {
        $dispatcher = new RouteDispatcher();

        return $dispatcher->default('\Vista\Router\Tests\Handlers\\');
    }

    /**
     * @dataProvider defaultRequestProvider
     * @depends testDefault
     */
    public function testDefaultHandle($request, $dispatcher)
    {
        $request = $this->getRequest($request['params']);
        $dispatcher->handle($request);
        
        $this->assertEquals($stub->executed(), true);
        $this->assertEquals($stub->result(), $request['result']);
    }

    private function getParamsMatchRouteModel()
    {
        return [
            'uri' => '/test_handler/process',
            'method' => 'post',
            'query' => ['var_get' => 123456],
            'parsed_body' => ['var_post' => 987654]
        ];
    }

    private function getParamsMatchServerRequest()
    {
        return [
            'uri' => '/test_handler/process',
            'method' => 'post',
            'query' => ['var_get' => 123456],
            'parsed_body' => ['var_post' => 987654]
        ];
    }

    private function getParamsMatchParams()
    {
        return [
            'uri' => '/test_handler/process',
            'method' => 'post',
            'query' => ['var_get' => 123456],
            'parsed_body' => ['var_post' => 987654]
        ];
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