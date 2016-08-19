<?php

namespace ModuleExtension\Features;

trait ScriptAccessFeature
{    
    protected $scripts = [];

    abstract public function executeScript();

    public function existsScript(string $name) 
    { 
        return isset($this->scripts[$name]); 
    }

    public function setScript(string $name, array $script)
    {
        $this->scripts[$name] = $script;
    }

    public function unsetScript(string $name)
    {
        if (isset($this->scripts[$name])) {
            unset($this->scripts[$name]);
        }
    }

    public function addScript(string $name)
    {
        $this->scripts[$name] = [];
    }

    public function addScriptCommand(string $name, string $command, array $params = [])
    {
        if (method_exists($this, $command)) {
            if (count($params) > 0) {
                $command = [$command => $params];
            } else {
                $command = (array)($command);
            }
            $this->scripts[$name][] = $command;
        } else {
            throw new RuntimeException('');
        }
    }

    public function removeScriptCommand(string $name, $index)
    {
        if (isset($this->scripts[$name][$index])) {
            unset($this->scripts[$name][$index]);
        } else {
            throw new RuntimeException('');
        }
    }
    
    protected function executeScriptCommand(array $script, array $params = [])
    {
        foreach ($script as $command_key => $command) {
            if (is_numeric(key($command))) {
                $method = current($command);
                $arguments = (array)($params[$command_key]) ?? [];
            } else {
                $method = key($command);
                $arguments = (array)(($params[$command_key] ?? current($command)));
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