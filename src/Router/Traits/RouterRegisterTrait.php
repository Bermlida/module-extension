<?php

namespace Vista\Router\Traits;

use RuntimeException;

// use Vista\Router\Traits\RouteCollectionAccessTrait, RouteCollectionAccessTrait;
//  implements Countable RouteManager
// use Application\Core\RepositoryBase;
// use Application\Models\Entities\Post;

trait RouterRegisterTrait
{

    protected $default_route;

    public function rule(string $name, string $path)
    {
        $route = new Route();
        $route->name($name)
                    ->path($path);

        return $route;
    }

    public function route(string $name, string $path, string $method)
    {
        $route = new Route();
        $route = $route->name($name)
                                    ->path($path)
                                    ->method($method);
        return $route;
    }

    public function default($setting)
    {
        $route = $this->default_route ?? new Route();
        if (is_array($setting)) {
            foreach ($setting as $property => $parameters) {
                call_user_func_array([$route, $property], (array)$parameters);
            }
        } elseif (is_callable($setting)) {
            $setting($route);
        }
        return $route;
    }
    
    public function group(string $name_prefix, string $path_prefix, callable $callback)
    {
        $route = new Route();
        $callback($route);
        return $route
    }


    public function options(array $options, $rule = null, $method = null)
    {
        $rule = $rule ?? $this->cache_rule;
        $method = $method ?? $this->cache_method;

        if (!empty($rule)) {
            if (!empty($method)) {
                $old_options = $this->options[$rule][$method]['options'];
                $this->callbacks[$rule][$method]['options'] = array_merge($old_options, $options);
            } else {                
                $old_options = $this->rules[$rule];
                $this->rules[$rule] = array_merge($old_options, $options);
            }
        } else {
            throw new RuntimeException('');
        }
        
        return $this;
    }

    public function register(string $rule, $method, $callback = null)
    {
        if (is_array($method)) {
            foreach ($method as $verb => $callback) {
                $verb = strtolower($verb);
                $this->$verb($rule, $callback);
            }
        } elseif (is_string($method) && $method != '') {
                $method = strtolower($method);
            if (is_string($callback) || is_callable($callback)) {
                $this->$method($rule, $callback);
            }
        }
    }

    public function __call($method, $arguments)
    {
        $valid_verb = ["post", "get", "put", "delete", "header", "patch", "options"];
        
        if (in_array($method, $valid_verb)) {
            $method = strtoupper($method);
            $keys = array_keys($this->rules, $arguments[0]);

            if (count($keys) > 0) {
                $key = current($keys);
                $this->callbacks[$key][$method] = $arguments[1];
            } else {
                $callbacks[$method] = $arguments[1];
                $this->rules[] = $arguments[0];
                $this->callbacks[] = $callbacks;
            }
        } else {
            throw new RuntimeException('');
        }
    }
}

/* End of file UserSetting.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/Entities/UserSetting.php */
