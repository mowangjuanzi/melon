<?php

namespace Melon;

use JetBrains\PhpStorm\Pure;
use Melon\Enums\HttpMethod;
use Melon\Supports\Str;
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

    public function get(string $uri, array $action)
    {
        $name = $action['as'] ?? null;

        // TODO http method 匹配
        $route = new Route($uri, $action);

        $this->routing->add($name ?: 'generate:' . Str::random(), $route);
    }

    public function dispatch(HttpMethod $method, string $uri = '')
    {
        $matcher = new UrlMatcher($this->routing, new RequestContext('', $method->name));

        return $matcher->match($uri);
    }
}
