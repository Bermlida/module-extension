<?php

namespace Vista\Router;

use RuntimeException;

trait RegisterRuleFeature extends Model
{

    public function rule(string $rule, $callbacks)
    {
        $this->cache_rule = $rule;
        $this->rules[$rule] = $this->rules[$rule] ?? [];

        if (is_array($callbacks)) {
            foreach ($callbacks as $method => $processor) {
                if (is_array($processor) && isset($processor['options'])) {
                    $this->callback($method, $processor[0])->option($processor['options']);
                } else {
                    $this->callback($method, $processor);
                }
            }
        } elseif (is_callable($callbacks)) {
            $callbacks($this);
        }

        return $this;
    }

    public function callback(string $method, $callback, $rule = null)
    {
        $rule = $rule ?? $this->cache_rule;

        if (is_callable($callback) || is_array($callback)) {
            $method = strtoupper($method);
            $this->cache_method = $method;
            $this->callbacks[$rule][$method]['handler'] = $callback;
        } else {
            throw new RuntimeException('');
        }

        return $this;
    }
    
    public function options(array $options, $rule = null, $method = null)
    {
        $rule = $rule ?? $this->cache_rule;
        $method = $method ?? $this->cache_method;

        if (!empty($rule)) {
            if (!empty($method)) {
                $old_options = $this->options[$rule][$method]['options'];
                $this->callbacks[$rule][$method] = array_merge($old_options, $options);
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
    
}

    

/* End of file UserSetting.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/Entities/UserSetting.php */
