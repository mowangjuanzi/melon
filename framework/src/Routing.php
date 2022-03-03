<?php

namespace Melon;

use JetBrains\PhpStorm\Pure;
use Melon\Enums\HttpMethodEnum;
use Melon\Enums\ResponseTypeEnum;
use Melon\Supports\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Routing
{
    protected readonly RouteCollection $routing;

    #[Pure]
    public function __construct()
    {
        $this->routing = new RouteCollection();
    }

    /**
     * 加载静态资源
     * @return void
     */
    public function loadStaticResponse(string $path)
    {
        $finder = new Finder();

        foreach ($finder->files()->in($path) as $item) {
            $this->get(substr($item->getPathname(), strlen($path)), ["type" => ResponseTypeEnum::STATIC]);
        }
    }

    /**
     * 注册 GET 请求
     * @param string $uri
     * @param array $action
     * @return void
     */
    public function get(string $uri, array $action)
    {
        if (!isset($action['type']) && isset($action['action'])) {
            $action['type'] = ResponseTypeEnum::CONTROLLER;
        }

        $name = $action['as'] ?? null;

        // TODO http method 匹配
        $route = new Route($uri, $action);

        $this->routing->add($name ?: 'generate:' . Str::random(), $route);
    }

    public function dispatch(HttpMethodEnum $method, string $uri = '')
    {
        $matcher = new UrlMatcher($this->routing, new RequestContext('', $method->name));

        return $matcher->match($uri);
    }
}
