<?php

namespace ModuleExtension\Constraints;

interface ServiceConstraint
{
    public function existsModule(string $name);

    public function setModule(string $name, $module, array $construct_params = []);

    public function unsetModule(string $name);

    public function existsMethodBind(string $name);

    public function bindMethod(string $name, Closure $callback, $module = null);

    public function unbindMethod(string $name);

    public function __call($name, $arguments);
}
