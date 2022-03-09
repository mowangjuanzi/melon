<?php

namespace Melon\Routing;

use Melon\Support\ServiceProvider;

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
        $this->application->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }
}
