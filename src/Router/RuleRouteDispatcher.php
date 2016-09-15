<?php

namespace Vista\Router;

use Vista\Router\Interfaces\RouteInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Vista\Router\Interfaces\RouteDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class RuleRouteDispatcher implements RouteDispatcherInterface
{//Register,  extends RouteManager extends
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

    protected function bindArguments(RouteInterface $route, ServerRequestInterface $request)
    {
        
    }

    protected function executeHandler($handler, $arguments)
    {
        switch (count($arguments)) {
            case 0:
                return $handler();
            case 1:
                return $handler($arguments[0]);
            case 2:
                return $handler($arguments[0], $arguments[1]);
            case 3:
                return $handler($arguments[0], $arguments[1], $arguments[2]);
            case 4:
                return $handler($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            case 5:
                return $handler($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
            default:
                return call_user_func_array($handler, $arguments);
        }
    }
}