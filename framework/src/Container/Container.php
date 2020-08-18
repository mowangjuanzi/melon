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
}
