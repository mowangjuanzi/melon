<?php

use Melon\Foundation\Application;
use Melon\Http\Response;

if (!function_exists("app")) {

    function app(): Application {
        return Application::getInstance();
    }
}

if (!function_exists("response")) {

    function response(?string $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}

if (!function_exists("storagePath")) {

    function storagePath(string $path = ''): string
    {
        return app()->storagePath($path);
    }
}
