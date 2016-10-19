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

        $stub->param_handlers = [
            'user' => function ($param) {
                return (object)['user_id' => $param];
            },
            'sort' => function ($param) {
                return $param * 10;
            },
            'top' => function ($param) {
                return round($param / 10, 1);
            }
        ];

        $this->setResolveHandlerMethod($stub);
        $this->setHandleParams($stub);
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
     * @depends clone testStub
     */
    public function testExecuteHandlerWithParams($request , $stub)
    {
        $stub->param_sources = [
            'sort' => 'get',
            'top' => 'post'
        ];

        $stub->handler = function ($sort, $top) {
            if ($sort == 220 && $top == 3.3) {
                return 'correct value';
            } else {
                return 'error value with params';
            }
        };

        $stub->method('resolveSources')->will(
            $this->returnCallback(function ($request) {
                return [
                    'sort' => $request->getQueryParams()['sort'],
                    'top' => $request->getParsedBody()['top']
                ];
            })
        );

        $stub->method('bindArguments')->will(
            $this->returnCallback(function ($params) {
                return [$params['sort'], $params['top']];
            })
        );

        $this->assertEquals($stub->executeHandler($request), 'correct value');
    }

    /**
     * @dataProvider  requestProvider
     * @depends clone testStub
     */
    public function testExecuteHandlerWithUri($request , $stub)
    {
        $stub->param_sources = [];

        $stub->handler = function ($user) {
            if ($user->user_id == 55) {
                return 'correct value';
            } else {
                return 'error value with uri';
            }
        };

        $stub->method('resolveUriSource')->will(
            $this->returnCallback(function ($request) {
                return ['user' => 55];
            })
        );

        $stub->method('bindArguments')->will(
            $this->returnCallback(function ($params) {
                return [$params['user']];
            })
        );

        $this->assertEquals($stub->executeHandler($request), 'correct value');
    }

    /**
     * @dataProvider  requestProvider
     * @depends clone testStub
     */
    public function testExecuteHandlerWithRequest($request , $stub)
    {
        $stub->param_sources = [];

        $stub->handler = function ($request) {
            $correct_get = $request->getQueryParams()['sort'] == 22;
            $correct_post = $request->getParsedBody()['top'] == 33;

            return ($correct_get && $correct_post) ? 'correct value' : 'error value with request';
        };

        $stub->method('resolveUriSource')->will(
            $this->returnCallback(function ($request) {
                return [];
            })
        );

        $stub->method('bindArguments')->will(
            $this->returnCallback(function ($params) {
                return [$params['request']];
            })
        );

        $this->assertEquals($stub->executeHandler($request), 'correct value');
    }

    private function setResolveHandlerMethod($stub)
    {
        $stub->method('resolveHandler')->will(
            $this->returnCallback(function ($handler) {
                return $handler;
            })
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

    private function setCallHandler($stub)
    {
        $stub->method('callHandler')->will(
            $this->returnCallback(function ($handler, $arguments) {
                return call_user_func_array($handler, $arguments);
            })
        );
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
}
