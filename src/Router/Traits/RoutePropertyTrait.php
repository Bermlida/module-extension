<?php

namespace Vista\Router\Traits;

// use Application\Core\RepositoryBase;
// use Application\Models\Entities\Post;

trait RoutePropertyTrait
{
    protected $name_prefix;

    protected $path_prefix;

    protected $name;

    protected $path;

    protected $method;

    protected $tokens = [];

    protected $parameter_handlers = [];

    protected $handler;
}