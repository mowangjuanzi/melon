<?php

namespace App\Http\Controllers;

use Melon\Response;

class HomeController
{
    public function index(): Response
    {
        return new Response("hello world");
    }
}
