<?php

namespace Melon\Events;

use Melon\Support\ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->application->register("events", EventDispatcher::class);
    }
}
