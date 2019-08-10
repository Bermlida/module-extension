<?php

namespace Vista\Router\Prototypes;

use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Vista\Router\Interfaces\RouteDispatcherInterface;

abstract class RouteDispatcherPrototype implements RouteDispatcherInterface
{
    /**
     * The namespace for the classes used as handler.
     *
     * @var string
     */
    protected $root_namespace;

    /**
     * The rules for match route and processing requests.
     *
     * @var \Vista\Router\Interfaces\RouteCollectionInterface
     */
    protected $rules;

    /**
     * The setting for customize the way to match route and process requests.
     *
     * @var mixed
     */
    protected $custom_setting;

    /**
     * How to handle the request.
     *
     * @var string
     */
    protected $cache_handle_type = '';

    /**
     * Has the handler that processes the request been executed?
     *
     * @var bool
     */
    protected $executed = false;

    /**
     * The result after processing request.
     *
     * @var mixed
     */
    protected $result;

    /**
     * Parse the uri to get the handler's class.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    abstract protected function getClass(ServerRequestInterface $request);

    /**
     * Parse the uri to get the handler's method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    abstract protected function getMethod(ServerRequestInterface $request);

    /**
     * According to the method of the handler, the required parameters are bound from the request content.
     *
     * @param array $handler
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    abstract protected function bindArguments(array $handler, ServerRequestInterface $request);

    /**
     * Handling requests by parsing uri to dispatch handlers, and set namespace for the handlers.
     *
     * @param string $root_namespace
     * @return $this
     */
    public function default(string $root_namespace)
    {
        $this->root_namespace = trim($root_namespace, '\\');
        $this->cache_handle_type = 'Default';
        $this->executed = false;

        return $this;
    }

    /**
     * Set and use the route collection to match the uri and handle the request.
     *
     * @param \Vista\Router\Interfaces\RouteCollectionInterface $rules
     * @return $this
     */
    public function rule(RouteCollectionInterface $rules)
    {
        $this->rules = $rules;
        $this->cache_handle_type = 'Rule';
        $this->executed = false;

        return $this;
    }

    /**
     * Handle requests using custom way and setting.
     *
     * @param mixed $custom_setting
     * @return $this
     */
    public function custom($custom_setting)
    {
        $this->custom_setting = $custom_setting;
        $this->cache_handle_type = 'Custom';
        $this->executed = false;

        return $this;
    }

    /**
     * Handle request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return $this
     * @throws \RuntimeException
     */
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
            }

            return $this;
        }

        throw new RuntimeException('');
    }

    /**
     * Get the value of "Has the handler that processes the request been executed".
     *
     * @return bool
     */
    public function executed()
    {
        return $this->executed;
    }

    /**
     * Get the value of "The result after processing request".
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function result()
    {
        if ($this->executed) {
            return $this->result;
        } else {
            throw new RuntimeException('');
        }
    }

    /**
     * Handling requests by parsing uri to dispatch handlers.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return void
     */
    protected function executeDefaultHandle(ServerRequestInterface $request)
    {
        $class = $this->root_namespace . '\\' . $this->getClass($request);
        $method = $this->getMethod($request);
        $arguments = $this->bindArguments([$class, $method], $request);
        
        if (class_exists($class) && method_exists($class, $method)) {
            $this->executed = true;
            $this->result = call_user_func_array([$class, $method], $arguments);
        } else {
            $this->executed = false;
        }
    }

    /**
     * Use the route collection to match the uri and handle the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return void
     */
    protected function executeRuleHandle(ServerRequestInterface $request)
    {
        $this->executed = false;
        
        foreach ($this->rules as $rule) {
            $judge_uri = $rule->matchUri($request);
            $judge_method = $rule->matchMethod($request);
            
            if ($judge_uri && $judge_method) {
                $this->executed = true;
                $this->result = $rule->executeHandler($request);
            }
        }
    }
}
