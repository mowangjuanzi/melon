<?php

namespace Melon\Log;

use Melon\Support\ServiceProvider;
use Symfony\Component\DependencyInjection\Reference;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->application->register("log", LogManager::class)->addArgument(new Reference("app"));
    }
}
