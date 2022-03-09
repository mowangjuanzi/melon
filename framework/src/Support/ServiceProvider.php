<?php

namespace Melon\Support;

use Melon\Foundation\Application;

abstract class ServiceProvider
{
    /**
     * All the registered booting callbacks.
     *
     * @var array
     */
    protected array $bootingCallbacks = [];

    public function __construct(protected Application $application)
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Call the registered booting callbacks.
     *
     * @return void
     */
    public function callBootingCallbacks()
    {
        $index = 0;

        while ($index < count($this->bootingCallbacks)) {
            $this->application->call($this->bootingCallbacks[$index]);

            $index++;
        }
    }
}
