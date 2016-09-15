<?php

namespace Vista\Router\Interfaces;

interface RouteInterface
{
    public function __call($name, $arguments);

    public function __get($name);
}
