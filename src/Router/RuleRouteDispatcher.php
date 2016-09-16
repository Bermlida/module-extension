<?php

namespace Vista\Router;

use Vista\Router\Interfaces\RouteInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Vista\Router\Interfaces\RouteDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class RuleRouteDispatcher implements RouteDispatcherInterface
{//extends extends
    protected $rules;

    // protected $match_rule;

    // protected $current_request;

    public function setRules(RouteCollectionInterface $rules)
    {
        $this->rules = $rules;
    }

    public function handle(ServerRequestInterface $request)
    {        
        foreach ($this->rules as $rule) {
            $judge_uri = preg_match('/' . $rule->regex . '/', $uri) === 1;
            $judge_method = in_array($request->getServerParams()['REQUEST_METHOD'], $rule->methods);

            if ($judge_uri && $judge_method) {
                $arguments = $this->bindArguments($rule, $request);
                $handler = $rule->handler_resolve;
                return $this->executeHandler($handler, $arguments);
            }
        }
        return null;
    }
}