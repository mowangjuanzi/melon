<?php

use Melon\Foundation\Application;

$application = new Application(dirname(__DIR__));

$application->singleton(App\Console\Kernel::class);

return $application;
