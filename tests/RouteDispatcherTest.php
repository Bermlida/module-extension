<?php

use Phly\Http\ServerRequest;
use Vista\Router\RouteDispatcher;

class RouteDispatcherTest extends PHPUnit_Framework_TestCase
{
    public function defaultRequestProvider()
    {
        return [
            'with route model' => [[
                'params' => [
                    'uri' => '/test_handler/process_with_model',
                    'method' => 'put',
                    'query' => ['item_name' => 'name', 'sort' => 22, 'user' => (object)['user_id' => 55]],
                    'parsed_body' => ['item_property' => 'first_name', 'top' => 33]
                ],
                'result' => ['name', 'first_name']
            ]],
            'with server request' => [[
                'params' => [
                    'uri' => '/test_handler/process_with_request',
                    'method' => 'patch',
                    'query' => ['class' => 'im'],
                    'parsed_body' => [
                        'IM104001' => 'John',
                        'IM104002' => 'Cheans'
                    ]
                ],
                'result' => [
                    'class' => 'im',
                    'IM104001' => 'John',
                    'IM104002' => 'Cheans'
                ]
            ]],
            'with params' => [[
                'params' => [
                    'uri' => '/test_handler/process',
                    'method' => 'get',
                    'query' => ['var_get' => 123456],
                    'parsed_body' => ['var_post' => 987654]
                ],
                'result' => (987654 - 123456)
            ]]
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
        $dispatcher->handle($this->getRequest($request['params']));
        
        $this->assertEquals($dispatcher->executed(), true);
        $this->assertEquals($dispatcher->result(), $request['result']);
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