<?php


namespace Melon\Framework\Foundation;


use Melon\Framework\Container\Container;

class Application extends Container
{
    /**
     * 基础目录
     * @var string
     */
    protected string $basePath = '';

    /**
     * 构造器
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }
}
