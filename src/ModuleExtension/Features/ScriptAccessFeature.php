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
}