<?php

namespace Melon\Foundation\Http;

use Melon\Foundation\Application;

abstract class Kernel
{
    public function __construct(protected Application $application)
    {

    }
}
