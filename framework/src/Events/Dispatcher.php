<?php

namespace Melon\Events;

use Melon\Foundation\Application;

class Dispatcher
{
    /**
     * Create a new event dispatcher instance.
     * @param Application $application
     */
    public function __construct(protected Application $application)
    {
    }
}
