<?php


namespace Melon\Framework;


use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * 版本号
     */
    const Version = "0.1";

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

        parent::__construct("melon", self::Version);
    }
}
