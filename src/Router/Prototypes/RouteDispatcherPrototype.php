<?php

namespace Vista\Router\Prototypes;

use RuntimeException;
use Vista\Router\Interfaces\RouteDispatcherInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class RouteDispatcherPrototype implements RouteDispatcherInterface
{
    protected $root_namespace;

    protected $rules;

    protected $custom_setting;

    protected $cache_handle_type = '';

    protected $executed = false;

    protected $result;

    public function default(string $root_namespace)
    {
        $this->root_namespace = $root_namespace;
        $this->cache_handle_type = 'Default';
        $this->executed = false;
        return $this;
    }

    public function rule(RouteCollectionInterface $rules)
    {
        $this->rules = $rules;
        $this->cache_handle_type = 'Rule';
        $this->executed = false;
        return $this;
    }

    public function custom($custom_setting)
    {
        $this->custom_setting = $custom_setting;
        $this->cache_handle_type = 'Custom';
        $this->executed = false;
        return $this;
    }

    public function handle(ServerRequestInterface $request)
    {
        if (!empty($this->cache_handle_type)) {
            $method = 'execute' . $this->cache_handle_type . 'Handle';

            if (method_exists($this, $method)) {
                if ($method == 'executeCustomHandle') {
                    $this->result = call_user_func([$this, $method], $request);
                    $this->executed = true;
                } else {
                    call_user_func([$this, $method], $request);
                }
                return $this;
            }
        }
        throw new RuntimeException('');
    }

    public function executed()
    {
        return $this->executed;
    }

    public function result()
    {
        if ($this->executed) {
            return $this->result;
        } else {
            throw new RuntimeException('');
        }
    }

    protected function executeDefaultHandle(ServerRequestInterface $request)
    {
        $uri = $request->getServerParams()['REQUEST_URI'];
        $uri_path = trim(parse_url($uri)['path'], '/');
        $segments = explode('/', $uri_path);
        
        $method = array_pop($segments);
        
        foreach ($segments as $key => $segment) {
            if (!(strpos($segment, '_') === false)) {
                $segment = implode(array_map(
                    function ($segment) {
                        $segment = ucfirst(strtolower($segment));
                        return $segment;
                    },
                    explode('_', $segment)
                ));
            } else {
                $segment = ucfirst($segment);
            }
            $segments[$key] = $segment;
        }
        $class = $this->root_namespace . '/' . implode('/', $segments);

        if (class_exists($class) && method_exists($class, $method)) {
            $this->executed = true;
            $this->result = call_user_func([$class, $method]);
        } else {
            $this->executed = false;
        }
    }

    protected function executeRuleHandle(ServerRequestInterface $request)
    {        
        foreach ($this->rules as $rule) {
            $judge_uri = $rule->matchUri($request);
            $judge_method = $rule->matchMethod($request);

            if ($judge_uri && $judge_method) {
                $this->executed = true;
                $this->result = $rule->executeHandler($request);
            }
        }
        
        $this->executed = false;
    }
}