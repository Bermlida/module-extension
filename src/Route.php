<?php

namespace Vista\Router;

use ReflectionMethod;
use ReflectionFunction;
use Psr\Http\Message\ServerRequestInterface;
use Vista\Router\Interfaces\RouteInterface;
use Vista\Router\Interfaces\RouteModelInterface;
use Vista\Router\Traits\RouteSetterTrait;
use Vista\Router\Traits\RouteGetterTrait;
use Vista\Router\Traits\RouteTrait;

class Route implements RouteInterface
{
    use RouteSetterTrait, RouteGetterTrait, RouteTrait;

    /**
     * The route name prefix.
     *
     * @var string
     */
    protected $name_prefix = '';

    /**
     * The route path prefix.
     *
     * @var string
     */
    protected $path_prefix = '';

    /**
     * The route name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The route path.
     *
     * @var string
     */
    protected $path = '';

    /**
     * The placeholder token name and its regex in route path.
     *
     * @var array
     */
    protected $tokens = [];

    /**
     * The route HTTP methods.
     *
     * @var array
     */
    protected $methods = [];

    /**
     * The handler deal with request if match route.
     *
     * @var \Closure|array
     */
    protected $handler;

    /**
     * Specify the source of the parameter that should be preprocessed.
     *
     * @var array
     */
    protected $param_sources = [];

    /**
     * Handlers for preprocessing specific parameters.
     *
     * @var array
     */
    protected $param_handlers = [];

    /**
     * Judge the Http method is valid.
     *
     * @param string $method
     * @return bool
     */
    protected function judgeValidMethod(string $method)
    {
        return true;
    }

    /**
     * Judge the regex is valid.
     *
     * @param string $regex
     * @return bool
     */
    protected function judgeValidRegex(string $regex)
    {
        return true;
    }

    /**
     * Judge the source is valid.
     *
     * @param string $source
     * @return bool
     */
    protected function judgeValidSource(string $source)
    {
        switch ($source) {
            case 'uri':
            case 'get':
            case 'post':
            case 'file':
            case 'cookie':
                return true;
            default:
                return false;
        }
    }

    /**
     * Judge the handler is valid.
     *
     * @param mixed $handler
     * @return bool
     */
    protected function judgeValidHandler($handler)
    {
        if (is_array($handler)) {
            if (is_object($handler[0]) || is_string($handler[0])) {
                if (!isset($handler[1])|| is_string($handler[1])) {
                    return true;
                }
            }
        } elseif (is_callable($handler)) {
            return true;
        }

        return false;
    }

    /**
     * Resolve the handler as an anonymous function.
     *
     * @param mixed $handler
     * @return \Closure|null|mixed
     */
    protected function resolveHandler($handler)
    {
        if (is_array($handler)) {
            $object = is_string($handler[0]) ? new $handler[0] : $handler[0];
            $method = $handler[1] ?? "__invoke";

            $reflector = new ReflectionMethod($object, $method);

            return $reflector->getClosure($object);
        }

        return $handler;
    }

    /**
     * Get parameters from sources.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    protected function resolveSources(ServerRequestInterface $request)
    {
        $original_data = [
            'get' => $request->getQueryParams(),
            'post' => $request->getParsedBody(),
            'file' => $request->getUploadedFiles(),
            'cookie' => $request->getCookieParams(),
            'uri' => $this->resolveUriSource($request)
        ];

        foreach ($this->param_sources as $item => $source) {
            if (isset($original_data[$source][$item])) {
                $params[$item] = $original_data[$source][$item];
            }
        }
        
        return $params;
    }

    /**
     * Use handler to process parameters.
     *
     * @param array $params
     * @return array
     */
    protected function handleParams(array $params)
    {
        foreach ($this->param_handlers as $item => $handler) {
            if (isset($params[$item])) {
                $handler = $this->resolveHandler($handler);
                $new_param = $this->callHandler($handler, [$params[$item]]);
                $params[$item] = $new_param;
            }
        }
        
        return $params;
    }

    /**
     * Bind the parameter values with the same name according to the handler's parameter list.
     *
     * @param array $params
     * @return array
     */
    protected function bindArguments(array $params)
    {
        $handler = $this->resolveHandler($this->handler);
        $parameters = (new ReflectionFunction($handler))->getParameters();
        
        if (!empty($parameters)) {
            if (count($parameters) == 1 && !is_null($reflector = $parameters[0]->getClass())) {
                if ($reflector->implementsInterface(RouteModelInterface::class)) {
                    $constructor = $reflector->getConstructor();

                    if (!is_null($constructor)) {
                        foreach ($constructor->getParameters() as $key => $parameter) {
                            if (isset($params[$parameter->name])) {
                                $value = $params[$parameter->name];
                                $arguments[$key] = $value;
                            }
                        }

                        $arguments = [$reflector->newInstanceArgs(($arguments ?? []))];
                    }
                } else {
                    if (isset($params[$parameters[0]->name])) {
                        $arguments[] = $params[$parameters[0]->name];
                    }
                }
            } else {
                foreach ($parameters as $key => $parameter) {
                    if (isset($params[$parameter->name])) {
                        $value = $params[$parameter->name];
                        $arguments[$key] = $value;
                    }
                }
            }
        }

        return $arguments ?? [];
    }

    /**
     * Call handler.
     *
     * @param Callable $handler
     * @param array $arguments
     * @return mixed
     */
    protected function callHandler(Callable $handler, array $arguments)
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

    /**
     * Resolve the uri source to get the parameters.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    protected function resolveUriSource(ServerRequestInterface $request)
    {
        $uri = $request->getServerParams()['REQUEST_URI'];
        $uri_path = trim(parse_url($uri)['path'], '/');
        $key_result = preg_match_all('/\{(\w+)\}/', $this->full_path, $key_matches);
        $value_result = preg_match('/' . $this->full_regex . '/', $uri_path, $value_matches);
        
        if ($key_result >= 1 && $value_result === 1) {
            unset($key_matches[0]);
            unset($value_matches[0]);
            return array_combine($key_matches[1], $value_matches);
        }

        return [];
    }
}
