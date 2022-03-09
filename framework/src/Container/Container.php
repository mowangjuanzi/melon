<?php

namespace Melon\Container;

use Closure;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

abstract class Container
{
    /**
     * 当前全局可用的容器
     *
     * @var static
     */
    protected static Container $instance;

    /**
     * 共享实例
     *
     * @var object[]
     */
    protected array $instances = [];

    /**
     * 注册的类型别名
     *
     * @var string[]
     */
    protected array $aliases = [];

    /**
     * 键是抽象名称的已注册别名
     *
     * @var array[]
     */
    protected array $abstractAliases = [];

    /**
     * 容器绑定
     *
     * @var array[]
     */
    protected array $bindings = [];

    /**
     * Register a shared binding in the container.
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return void
     */
    public function singleton(string $abstract, Closure|string $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Register a binding with the container.
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @param bool $shared
     * @return void
     */
    public function bind(string $abstract, Closure|string $concrete = null, bool $shared = false)
    {

    }

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string $abstract
     * @param  mixed  $instance
     * @return mixed
     */
    public function instance(string $abstract, mixed $instance): mixed
    {
        $this->removeAbstractAlias($abstract);

        $this->instances[$abstract] = $instance;

        return $instance;
    }

    /**
     * Remove an alias from the contextual binding alias cache.
     *
     * @param string $searched
     * @return void
     */
    protected function removeAbstractAlias(string $searched)
    {
        if (! isset($this->aliases[$searched])) {
            return;
        }

        foreach ($this->abstractAliases as $abstract => $aliases) {
            foreach ($aliases as $index => $alias) {
                if ($alias == $searched) {
                    unset($this->abstractAliases[$abstract][$index]);
                }
            }
        }
    }

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

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []): mixed
    {
        return $this->resolve($abstract, $parameters);
    }

    /**
     * Resolve the given type from the container.
     *
     * @param string|Closure $abstract
     * @param array $parameters
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function resolve(string|Closure $abstract, array $parameters = []): mixed
    {
        $abstract = $this->getAlias($abstract);

        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // 获取参数列表
        try {
            $reflector = new ReflectionClass($abstract);
        } catch (ReflectionException $e) {
            throw new BindingResolutionException("Target class [$abstract] does not exist.", 0, $e);
        }

        if (! $reflector->isInstantiable()) {
            $this->notInstantiable($abstract);
        }

        $constructor = $reflector->getConstructor();
        if ($constructor) {
            $dependencies = $constructor->getParameters();

            $args = $this->resolveDependencies($dependencies, $parameters);
        } else {
            $args = [];
        }
        $this->instances[$abstract] = new $abstract(...$args);

        return $this->instances[$abstract];
    }

    /**
     * Get the alias for an abstract if available.
     *
     * @param string $abstract
     * @return string
     */
    public function getAlias(string $abstract): string
    {
        return isset($this->aliases[$abstract])
            ? $this->getAlias($this->aliases[$abstract])
            : $abstract;
    }

    /**
     * Throw an exception that the concrete is not instantiable.
     *
     * @param string $concrete
     * @return void
     *
     * @throws BindingResolutionException
     */
    protected function notInstantiable(string $concrete)
    {
        if (! empty($this->buildStack)) {
            $previous = implode(', ', $this->buildStack);

            $message = "Target [$concrete] is not instantiable while building [$previous].";
        } else {
            $message = "Target [$concrete] is not instantiable.";
        }

        throw new BindingResolutionException($message);
    }

    /**
     * Resolve all the dependencies from the ReflectionParameters.
     *
     * @param ReflectionParameter[] $dependencies
     * @param array $parameters
     * @return array
     * @throws BindingResolutionException
     */
    protected function resolveDependencies(array $dependencies, array $parameters): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            if (isset($parameters[$dependency->getName()])) {
                $results[] = $parameters[$dependency->getName()];
            } else {
                $results[] = $this->make($dependency->getType()->getName());
            }
        }

        return $results;
    }
}
