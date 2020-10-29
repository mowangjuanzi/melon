<?php


namespace Melon;


use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * @var Application
     */
    protected static Application $instance;

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

        $this->setInstance();

        $this->loadConfig();
        $this->registerCommands();
    }

    /**
     * 加载配置
     */
    protected function loadConfig()
    {
        $dir = new \RecursiveDirectoryIterator($this->basePath . "/config", \FilesystemIterator::SKIP_DOTS);
        foreach ($dir as $item) {
            if ($item->isFile() && $item->getExtension() == "php") {
                $this->config[$item->getBasename(".php")] = require $item->getPathname();
            }
        }
    }

    /**
     * 获取配置
     * @param string $path
     * @return mixed
     */
    protected function getConfig(string $path)
    {
        $config = $this->config;
        foreach (explode(".", $path) as $item) {
            if (is_array($config) && isset($config[$item])) {
                $config = $config[$item];
            } else {
                break;
            }
        }

        return $config;
    }

    /**
     * 注册命令
     */
    protected function registerCommands()
    {
        foreach ($this->getConfig("app.commands") as $item) {
            $this->add(new $item);
        }
    }

    /**
     * 设置实例
     * @return bool
     */
    protected function setInstance(): bool
    {
        self::$instance = $this;
        return true;
    }

    /**
     * 获取共享实例
     * @return Application
     */
    public static function getInstance(): Application
    {
        return self::$instance;
    }
}
