<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        return \response("hi, current datetime is " . date("Y-m-d H:i:s"));
    }
}
