<?php

use App\Http\Controllers\HomeController;
use Melon\Routing;

/** @var Routing $routing */

$routing->get("/", ["action" => [HomeController::class, "index"]]);
