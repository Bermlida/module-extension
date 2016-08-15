<?php

namespace ModuleExtension\Foundations;

use ModuleExtension\Features\ScriptAccessFeature;

abstract class Library
{
    use ScriptAccessFeature;
    
    public function executeScript($script = "default", array $params = [])
    {
        $available_script = $this->scripts;
        if ((is_string($script) || is_array($script)) && !empty($script)) {
            $script = is_string($script) && isset($available_script[$script])
                         ? $available_script[$script]
                         : (is_array($script) ? $script : []);
            
            foreach ($script as $command_key => $command) {
                if (is_numeric(key($command))) {
                    $method = current($command);
                    $arguments = $params[$command_key] ?? [];
                } else {
                    $method = key($command);
                    $arguments = $params[$command_key] ?? (array)(current($command));
                }
                
                switch (count($arguments)) {
                    case 0:
                        $this->$method();
                        break;
                    case 1:
                        $this->$method($arguments[0]);
                        break;
                    case 2:
                        $this->$method($arguments[0], $arguments[1]);
                        break;
                    case 3:
                        $this->$method($arguments[0], $arguments[1], $arguments[2]);
                        break;
                    case 4:
                        $this->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
                        break;
                    case 5:
                        $this->$method($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
                        break;
                    default:
                        call_user_func_array([$this, $method], $arguments);
                        break;
                }
            }
        }
    }
}