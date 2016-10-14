<?php

namespace Vista\Router\Interfaces;

use Vista\Router\Interfaces\RouteCollection;
use Psr\Http\Message\ServerRequestInterface;

interface RouteDispatcherInterface
{
    public function default(string $root_namespace);

    public function rule(RouteCollectionInterface $rules);

    public function custom($custom_setting);

    public function handle(ServerRequestInterface $request);

    public function executed();

    public function result();
}
