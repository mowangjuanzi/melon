<?php

use App\Http\Controllers\HomeController;
use Melon\Routing\Routing;

/** @var Routing $routing */

$routing->get("/", ["action" => [HomeController::class, "index"]]);
