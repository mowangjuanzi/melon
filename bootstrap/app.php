<?php

use Melon\Framework\Contracts\Console\Kernel;
use Melon\Framework\Foundation\Application;

$app = new Application();

$app->singleton(Kernel::class, \App\Console\Kernel::class);

return $app;
