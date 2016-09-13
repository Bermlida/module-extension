<?php

namespace Vista\Router;

use Vista\Router\Route;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Vista\Router\Interfaces\RouteDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

class RuleRouteDispatcher implements RouteDispatcherInterface
{//Register,  extends Authenticatable RouteManager extends ModelBase
    protected $rules;

    protected $match_rule;

    protected $current_request;

    public function get(EntityConstraint $entity)
    {
        
    }

    public function setRules(RouteCollectionInterface $rules)
    {
        $this->rules = $rules;
    }

    public function handle(ServerRequestInterface $request)
    {        
        foreach ($this->rules as $rule) {

            if (preg_match('/' . $pattern . '/', $uri) === 1) {
                return $rule;
            }
        }
        return null;
    }

    protected function matchUri(Route $rule)
    {
        $pattern = preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $matches) use ($rule->tokens) {
                    unset($matches[0]);
                    foreach ($matches as $key => $token) {
                    }
                    $token = $matches[1];
                    return '(' .  ($rule->tokens[$token] ?? '\w+') . ')';
            },
            str_replace('/', '\/', $rule->path)
        );
        
    }
}