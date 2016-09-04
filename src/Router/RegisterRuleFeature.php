<?php

namespace Vista\Router;

use RuntimeException;

trait RegisterRuleFeature extends Model
{
    public function rule(string $rule)
    {
        $keys = array_keys($this->rules, $rule);
        if (count($keys) === 0) {
            $this->rules[] = $rule;
        }
    }

    // public function registerCallback(string $method, $callback)
    // {
    //     if (is_callable($callback) && (is_string($callback) && !empty($callback))) {

    //     } else {
    //         throw new RuntimeException('');
    //     }
    // }

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
    
    protected function register(string $rule, string $method, array $processor)
    {
        // $keys = array_keys($this->rules, $rule);
        $method = strtoupper($method);
        $this->rules[$rule][$method] = $processor;
        if (in_array($method, $valid_verb)) {
            
        } else {
            throw new RuntimeException('');
        }
        
        if (count($keys) > 0) {
            $key = current($keys);
                $this->callbacks[$key][$method] = $arguments[1];
        } else {
                $callbacks[$method] = $arguments[1];
                $this->rules[] = $arguments[0];
                $this->callbacks[] = $callbacks;
        }
    }
    
}

    

/* End of file UserSetting.php */
/* Location: .//home/tkb-user/projects/laravel/app/Repositories/Entities/UserSetting.php */
