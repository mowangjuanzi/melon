<?php

namespace Melon\Log;

use Melon\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->application->singleton('log', function ($app) {
            return new LogManager($app);
        });
    }
}
