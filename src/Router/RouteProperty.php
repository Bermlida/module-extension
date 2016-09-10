<?php

namespace Vista\Router;

// use Application\Core\RepositoryBase;
// use Application\Models\Entities\Post;

trait RouteProperty extends RepositoryBase
{
    protected $name_prefix;

    protected $path_prefix;

    protected $name;

    protected $path;

    // protected $segments = [];

    protected $tokens = [];

    protected $parameter_handlers = [];

    protected $handler;
}