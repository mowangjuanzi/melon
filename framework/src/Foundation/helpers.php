<?php

use Melon\Foundation\Application;
use Melon\Http\Response;

if (!function_exists("app")) {

    /**
     * 获取可用的容器实例。
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return Application
     */
    function app(string $abstract = null, array $parameters = []): Application {
        if (is_null($abstract)) {
            return Application::getInstance();
        }

        return Application::getInstance()->make($abstract, $parameters);
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
