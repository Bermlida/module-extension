<?php

namespace Vista\Router;

class Route extends EntityBase
{
    use RouteTrait;

    protected $name_prefix;

    protected $path_prefix;

    protected $name;

    protected $path;

    // protected $segments = [];

    protected $tokens = [];

    protected $parameter_handlers = [];

    protected $handler;
}
