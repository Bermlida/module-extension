<?php

namespace Vista\Router;

use Vista\Router\Interfaces\RouteDispatcherInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Psr\Http\Message\ServerRequestInterface;

class RuleRouteDispatcher extends
{
    protected $rules = [];

    protected function setRules(RouteCollectionInterface $rules)
    {
        $this->rules = $rules;
    }

    public function handle(ServerRequestInterface $request)
    {        
        foreach ($this->rules as $rule) {
            $judge_uri = $rule->matchUri($request);
            $judge_method = $rule->matchMethod($request);

            if ($judge_uri && $judge_method) {
                return $rule->executeHandler($request);
            }
        }
        return null;
    }
}