<?php

namespace Melon\Routing;

use Melon\Support\ServiceProvider;
use Symfony\Component\DependencyInjection\Reference;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
    }

    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->application->register("router", Router::class)->addArgument(new Reference("events"))->addArgument(new Reference("app"));
    }
}
