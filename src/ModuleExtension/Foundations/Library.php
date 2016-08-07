<?php

namespace ModuleExtension\Foundations;

abstract class Library
{
    protected $script_result = null;

    public function script()
    {
        return [];
    }

    protected function getScriptResult()
    {
        return $this->script_result;
    }

    protected function setScriptResult($result)
    {
        if (!is_null($result)) {
            $this->script_result = $result;
        }
    }

    protected function resetScriptResult()
    {
        $this->script_result = null;
    }

    public function executeScript($script = "default", array $params = [])
    {
        $available_script = $this->script();
        if ((is_string($script) || is_array($script)) && !empty($script)) {
            $script = is_string($script) && isset($available_script[$script])
                         ? $available_script[$script]
                         : (is_array($script) ? $script : []);
            
            foreach ($script as $command_key => $command) {
                if (is_numeric(key($command))) {
                    $command_function = current($command);
                    $command_params = $params[$command_key] ?? [];
                } else {
                    $command_function = key($command);
                    $command_params = $params[$command_key] ?? (array)(current($command));
                }
                
                switch (count($command_params)) {
                    case 0:
                        $this->$command_function();
                    case 1:
                        $this->$command_function($command_params[0]);
                    case 2:
                        $this->$command_function($command_params[0], $command_params[1]);
                    case 3:
                        $this->$command_function($command_params[0], $command_params[1], $command_params[2]);
                    default:
                        call_user_func_array([$this, $command_function], $command_params);
                }
            }

            if (!is_null($this->script_result)) {
                $temp_result = $this->script_result;
                $this->script_result = null;
                return $temp_result;
            }
        }
    }
}