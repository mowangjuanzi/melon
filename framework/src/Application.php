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
     * 配置
     * @var array
     */
    protected array $config = [];

    /**
     * 构造器
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;

        parent::__construct("melon", self::Version);

        $this->loadConfig();
        $this->registerCommands();
    }

    /**
     * 加载配置
     */
    protected function loadConfig()
    {
        $dir = new \RecursiveDirectoryIterator($this->basePath . "/config");
        foreach ($dir as $item) {
            if ($item->isFile()) {
                $path = $item->getPath();
            }
        }
    }

    /**
     * 注册命令
     */
    protected function registerCommands()
    {

    }
}
