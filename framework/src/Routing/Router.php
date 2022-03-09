<?php

namespace Melon\Routing;

use Melon\Events\Dispatcher;
use Melon\Foundation\Application;

class Router
{
    /**
     * The route collection instance.
     *
     * @var RouteCollection
     */
    protected readonly RouteCollection $routes;

    /**
     * Create a new Router instance.
     *
     * @param Dispatcher $events
     * @param Application $container
     * @return void
     */
    public function __construct(protected Dispatcher $events, protected Application $container)
    {
        $this->routes = new RouteCollection();
    }
}
