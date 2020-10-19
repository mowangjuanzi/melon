<?php


namespace Melon\Framework\Container;


use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * 实例集合
     * @var array
     */
    protected array $instances = [];

    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function has($id)
    {
        // TODO: Implement has() method.
    }

    /**
     * 从类型中解析给定的类型
     * @param string $abstract
     */
    protected function resolve(string $abstract)
    {

    }

    /**
     * 在容器内注册共享绑定
     * @param string $abstract
     * @param $concrete
     */
    public function singleton(string $abstract, $concrete)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * 在容器内注册绑定
     * @param string $abstract
     * @param null $concrete
     * @param bool $shared
     */
    public function bind(string $abstract, $concrete = null, bool $shared = false)
    {
        // TODO 还没写这一块的功能
    }
}