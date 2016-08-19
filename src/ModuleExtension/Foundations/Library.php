<?php

namespace ModuleExtension\Foundations;

use ModuleExtension\Features\ScriptAccessFeature;

abstract class Library
{
    use ScriptAccessFeature;
    
    public function executeScript($script = "default", array $params = [])
    {
        if ((is_string($script) || is_array($script)) && !empty($script)) {
            $script = is_string($script) && isset($this->scripts[$script])
                         ? $this->scripts[$script]
                         : (is_array($script) ? $script : []);
            
            $this->executeScriptCommand($script, $params);
        }
    }
}