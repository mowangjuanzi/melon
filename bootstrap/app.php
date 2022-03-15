<?php

use Melon\Foundation\Application;
use Symfony\Component\DependencyInjection\Reference;

$application = new Application(dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$application->register(App\Console\Kernel::class, App\Console\Kernel::class)->addArgument(new Reference("app"));

$application->register(App\Http\Kernel::class, App\Http\Kernel::class)->addArgument(new Reference("app"));

$application->register(App\Exceptions\Handler::class, App\Exceptions\Handler::class);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script, so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $application;
