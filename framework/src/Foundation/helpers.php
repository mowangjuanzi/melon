<?php

use Melon\Foundation\Application;
use Melon\Http\Response;

if (!function_exists("app")) {
    /**
     * 获取可用的容器实例。
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return Application|mixed
     */
    function app(string $abstract = null, array $parameters = []): mixed {
        if (is_null($abstract)) {
            return Application::getInstance();
        }

        return Application::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists("response")) {
    /**
     * Return a new response from the application.
     * @param string|null $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    function response(?string $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}

if (! function_exists('app_path')) {
    /**
     * Get the path to the application folder.
     *
     * @param string $path
     * @return string
     */
    function app_path(string $path = ''): string
    {
        return app()->path($path);
    }
}

if (! function_exists('base_path')) {
    /**
     * Get the path to the base of the install.
     *
     * @param string $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        return app()->basePath($path);
    }
}

if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path(string $path = ''): string
    {
        return app()->configPath($path);
    }
}

if (! function_exists('resource_path')) {
    /**
     * Get the path to the resources folder.
     *
     * @param string $path
     * @return string
     */
    function resource_path(string $path = ''): string
    {
        return app()->resourcePath($path);
    }
}

if (! function_exists('storage_path')) {
    /**
     * Get the path to the storage folder.
     *
     * @param string $path
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return app()->storagePath($path);
    }
}
