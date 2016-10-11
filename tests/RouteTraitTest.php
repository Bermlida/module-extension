<?php

use Vista\Router\Traits\RouteTrait;
use Phly\Http\ServerRequest;

class RouteTraitTest extends PHPUnit_Framework_TestCase
{
    public function requestProvider()
    {
        return array_map([$this, 'getRequest'], [
            [
                'uri' => '/users/55/account/profiles/picture/name',
                'method' => 'get',
                'query' => ['sort' => 22],
                'parsed_body' => ['top' => 33]
            ]
        ]);
    }

    public function testStub()
    {
        $stub = $this->getMockForTrait(RouteTrait::class);

        $stub->full_regex = 'users\/(\d+)\/account\/profiles\/(\w+)\/(name|value)';
        $stub->methods = ['get', 'options', 'header'];

        $stub->param_sources = ['get', 'post', 'file'];
        $stub->param_handlers = [
            'sort' => function ($param) {
                return $param * 10;
            },
            'top' => function ($param) {
                return $param / 10;
            }
        ];
        $stub->handler = function ($sort, $top) {
            if ($sort > 100 && $top < 10) {
                return 'correct value';
            } else {
                return 'error value';
            }
        };

        $this->setResolveHandlerMethod($stub);
        $this->setResolveSourcesMethod($stub);
        $this->setHandleParams($stub);
        $this->setBindArguments($stub);
        $this->setCallHandler($stub);

        return $stub;
    }

    /**
     * @dataProvider  requestProvider
     * @depends testStub
     */
    public function testMatchUri($request, $stub)
    {
        $this->assertEquals($stub->matchUri($request), true);
    }

    /**
     * @dataProvider  requestProvider
     * @depends  testStub
     */
    public function testMatchMethod($request , $stub)
    {
        $this->assertEquals($stub->matchMethod($request), true);
    }
    
    /**
     * @dataProvider  requestProvider
     * @depends testStub
     */
    public function testExecuteHandler($request , $stub)
    {
        $this->assertEquals($stub->executeHandler($request), 'correct value');
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

        return [$request];
    }

    private function setResolveHandlerMethod($stub)
    {
        $stub->method('resolveHandler')->will(
            $this->returnCallback(
                function ($handler) {
                    return $handler;
                }
            )
        );
    }

    private function setResolveSourcesMethod($stub)
    {
        $stub->method('resolveSources')->will(
            $this->returnCallback(
                function ($request) {
                    $items = [];

                    $items = array_merge($items, $request->getUploadedFiles());
                    $items = array_merge($items, $request->getParsedBody());
                    $items = array_merge($items, $request->getQueryParams());

                    return $items;
                }
            )
        );
    }

    private function setHandleParams($stub)
    {
        $stub->method('handleParams')->will(
            $this->returnCallback(
                function ($params) use ($stub) {
                    foreach ($params as $key => $value) {
                        if (isset($stub->param_handlers[$key])) {
                            $params[$key] = $stub->param_handlers[$key]($value);
                        }
                    }

                    return $params;
                }
            )
        );
    }

    private function setBindArguments($stub)
    {
        $stub->method('bindArguments')->will(
            $this->returnCallback(
                function ($params) {
                    return [
                        0 => $params['sort'] ?? null,
                        1 => $params['top'] ?? null
                    ];
                }
            )
        );
    }

    private function setCallHandler($stub)
    {
        $stub->method('callHandler')->will(
            $this->returnCallback(
                function ($handler, $arguments) {
                    return $handler($arguments[0], $arguments[1]);
                }
            )
        );
    }
}
