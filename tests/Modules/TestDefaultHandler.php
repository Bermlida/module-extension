<?php

namespace Vista\Router\Tests\Modules;

use Phly\Http\ServerRequest;

/**
 * @codeCoverageIgnore
 */
class TestDefaultHandler
{
    public function process(ServerRequest $request)
    {
        return $request;
    }

    public function putProcessWithModel(TestDefaultRouteModel $model)
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