<?php

namespace Vista\Router\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use Vista\Router\Interfaces\RouteCollection;

interface RouteDispatcherInterface
{
    /**
     * Handling requests by parsing uri to dispatch handlers, and set namespace for the handlers.
     *
     * @param string $root_namespace
     * @return $this
     */
    public function default(string $root_namespace);

    /**
     * Set and use the route collection to match the uri and handle the request.
     *
     * @param \Vista\Router\Interfaces\RouteCollectionInterface $rules
     * @return $this
     */
    public function rule(RouteCollectionInterface $rules);

    /**
     * Handle requests using custom way and setting.
     *
     * @param mixed $custom_setting
     * @return $this
     */
    public function custom($custom_setting);

    /**
     * Handle request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return $this
     * @throws \RuntimeException
     */
    public function handle(ServerRequestInterface $request);

    /**
     * Get the value of "Has the handler that processes the request been executed".
     *
     * @return bool
     */
    public function executed();

    /**
     * Get the value of "The result after processing request".
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function result();
}
