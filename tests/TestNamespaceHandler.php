<?php

namespace Vista\Router\Tests\Handlers;

use TestRouteModel;
use Phly\Http\ServerRequest;
use Vista\Router\Interfaces\RouteModelInterface;

class TestHandler
{
    public function process(ServerRequest $request)
    {
        return $request;
    }

    public function putProcessWithModel(TestRouteModel $model)
    {
        return [
            $model->item_name,
            $model->item_property
        ];
    }

    public function patchProcessWithRequest(ServerRequest $request)
    {
        return array_merge(
            $request->getQueryParams(),
            $request->getParsedBody()
        );
    }

    public function getProcess(int $var_get, int $var_post)
    {
        return $var_post - $var_get;
    }
}