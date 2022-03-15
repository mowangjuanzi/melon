<?php

namespace Melon\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class Container extends ContainerBuilder
{
    /**
     * 当前全局可用的容器
     *
     * @var static
     */
    protected static Container $instance;

    /**
     * Set the shared instance of the container.
     *
     * @param Container $container
     * @return static
     */
    public static function setInstance(Container $container): static
    {
        return static::$instance = $container;
    }

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance(): static
    {
        return static::$instance;
    }
}
