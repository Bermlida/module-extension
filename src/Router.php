<?php

namespace Vista\Router;

use RuntimeException;
use Psr\Http\Message\ServerRequestInterface;
use Vista\Router\Interfaces\RouteCollectionInterface;
use Vista\Router\Interfaces\RouteDispatcherInterface;

class Router
{
    /**
     * The namespace for the classes used as handler.
     *
     * @var string
     */
    protected $root_namespace;

    /**
     * The setting for customize the way to match route and process requests.
     *
     * @var mixed
     */
    protected $custom_setting;

    /**
     * The default setting for all routes.
     *
     * @var \Vista\Router\Route
     */
    protected $default_route;

    /**
     * The route instance used in the router's method chaining.
     *
     * @var \Vista\Router\Route
     */
    protected $cache_route;

    /**
     * The value of path prefix used when adding route.
     *
     * @var string
     */
    protected $cache_path_prefix = '';

    /**
     * The value of name prefix used when adding route.
     *
     * @var string
     */
    protected $cache_name_prefix = '';

    /**
     * The route collection instance.
     *
     * @var \Vista\Router\Interfaces\RouteCollectionInterface
     */
    protected $collection;

    /**
     * The route dispatcher instance.
     *
     * @var \Vista\Router\Interfaces\RouteDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Create a new router instance.
     *
     * @param \Vista\Router\Interfaces\RouteCollectionInterface $collection
     * @param \Vista\Router\Interfaces\RouteDispatcherInterface $dispatcher
     * @return void
     */
    public function __construct(
        RouteCollectionInterface $collection,
        RouteDispatcherInterface $dispatcher
    ) {
        $this->collection = $collection;

        $this->dispatcher = $dispatcher;
    }

    /**
     * Set the namespace for the classes used as handler.
     *
     * @param string $root_namespace
     * @return $this
     */
    public function setRootNamespace(string $root_namespace)
    {
        $this->root_namespace = $root_namespace;

        return $this;
    }

    /**
     * Set the setting for customize the way to match route and process requests.
     *
     * @param mixed $custom_setting
     * @return $this
     */
    public function setCustomSetting($custom_setting)
    {
        $this->custom_setting = $custom_setting;

        return $this;
    }

    /**
     * Add a new "options" route with the router.
     *
     * @param string $path
     * @param mixed|null $handler
     * @return $this
     */
    public function options(string $path, $handler = null)
    {
        return $this->route($path, 'options', $handler);
    }

    /**
     * Add a new "head" route with the router.
     *
     * @param string $path
     * @param mixed|null $handler
     * @return $this
     */
    public function head(string $path, $handler = null)
    {
        return $this->route($path, 'head', $handler);
    }

    /**
     * Add a new "get" route with the router.
     *
     * @param string $path
     * @param mixed|null $handler
     * @return $this
     */
    public function get(string $path, $handler = null)
    {
        return $this->route($path, 'get', $handler);
    }

    /**
     * Add a new "put" route with the router.
     *
     * @param string $path
     * @param mixed|null $handler
     * @return $this
     */
    public function put(string $path, $handler = null)
    {
        return $this->route($path, 'put', $handler);
    }

    /**
     * Add a new "delete" route with the router.
     *
     * @param string $path
     * @param mixed|null $handler
     * @return $this
     */
    public function delete(string $path, $handler = null)
    {
        return $this->route($path, 'delete', $handler);
    }

    /**
     * Add a new "post" route with the router.
     *
     * @param string $path
     * @param mixed|null $handler
     * @return $this
     */
    public function post(string $path, $handler = null)
    {
        return $this->route($path, 'post', $handler);
    }

    /**
     * Add a new "patch" route with the router.
     *
     * @param string $path
     * @param mixed|null $handler
     * @return $this
     */
    public function patch(string $path, $handler = null)
    {
        return $this->route($path, 'patch', $handler);
    }

    /**
     * Add a new route with the router.
     *
     * @param string $path
     * @param string|array|mixed $methods
     * @param mixed|null $handler
     * @return $this
     */
    public function route(string $path, $methods, $handler = null)
    {
        $route = $this->registerRoute();

        $route->path($path)->methods($methods);

        if (!is_null($handler)) {
            $route->handler($handler);
        }

        if (!empty($this->cache_path_prefix)) {
            $route->path_prefix($this->cache_path_prefix);
        }

        if (!empty($this->cache_name_prefix)) {
            $route->name_prefix($this->cache_name_prefix);
        }

        $this->collection[] = $route;
        $this->cache_route = $route;

        return $this;
    }
    
    /**
     * Create a route group with path prefix and name prefix.
     *
     * @param string $path_prefix
     * @param callable $callback
     * @param string $name_prefix
     * @return $this
     */
    public function group(string $path_prefix, callable $callback, string $name_prefix = '')
    {
        $this->cache_path_prefix = $path_prefix;
        $this->cache_name_prefix = $name_prefix;
        
        $callback($this);

        $this->cache_path_prefix = '';
        $this->cache_name_prefix = '';

        return $this;
    }

    /**
     * Set the default setting for all routes.
     *
     * @param mixed|null $callback
     * @return $this
     */
    public function default($callback = null)
    {
        $route = $this->registerRoute();

        $this->cache_route = $route;

        if (is_callable($callback)) {
            $callback($this);
        }

        $this->default_route = $route;

        return $this;
    }

    /**
     * Dispatch the request to the handler and get the handling result.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return mixed
     * @throws \RuntimeException
     */
    public function dispatch(ServerRequestInterface $request)
    {
        if ($this->dispatcher->custom($this->custom_setting)->handle($request)->executed()) {
            return $this->dispatcher->result();
        } elseif ($this->dispatcher->rule($this->collection)->handle($request)->executed()) {
            return $this->dispatcher->result();
        } elseif ($this->dispatcher->default($this->root_namespace)->handle($request)->executed()) {
            return $this->dispatcher->result();
        } else {
            throw new RuntimeException('');
        }
    }

    /**
     * Register a new route with the router and get it.
     *
     * @return \Vista\Router\Route
     */
    protected function registerRoute()
    {
        $route = is_object($this->default_route) ? clone $this->default_route : new Route();

        return $route;
    }

    /**
     * Handle dynamic method calls in the router.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \RuntimeException
     */
    public function __call($method, $arguments)
    {   // && method_exists($this->cache_route, $method)
        if (is_object($this->cache_route)) {
            switch (count($arguments)) {
                case 0:
                    $this->cache_route->$method();
                    break;
                case 1:
                    $this->cache_route->$method($arguments[0]);
                    break;
                case 2:
                    $this->cache_route->$method($arguments[0], $arguments[1]);
                    break;
                case 3:
                    $this->cache_route->$method($arguments[0], $arguments[1], $arguments[2]);
                    break;
                case 4:
                    $this->cache_route->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                    break;
                default:
                    call_user_func_array([$this->cache_route, $method], $arguments);
                    break;
            }
        }
        
        return $this;
    }
}
