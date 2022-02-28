<?php


namespace Melon;


use FilesystemIterator;
use Melon\Commands\ServerStartCommand;
use Melon\Events\BaseEvent;
use RecursiveDirectoryIterator;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Output\ConsoleOutput;

class Application extends ConsoleApplication
{
    /**
     * 应用常驻
     * @var Application
     */
    protected static Application $instance;

    /**
     * 事件
     * @var BaseEvent
     */
    public readonly BaseEvent $event;

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
     * 内置命令列表
     * @var array|string[]
     */
    protected array $commands = [
        ServerStartCommand::class,
    ];

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

        $this->registerEvent();
    }

    /**
     * 加载配置
     */
    protected function loadConfig()
    {
        $dir = new RecursiveDirectoryIterator($this->basePath . "/config", FilesystemIterator::SKIP_DOTS);
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
    public function getConfig(string $path): mixed
    {
        $config = $this->config;
        foreach (explode(".", $path) as $item) {
            $config = $config[$item] ?? null;
        }

        return $config;
    }

    /**
     * 注册命令
     */
    protected function registerCommands()
    {
        foreach ($this->commands as $item) {
            $this->add(new $item);
        }
    }

    /**
     * 注册事件
     * @return void
     */
    protected function registerEvent()
    {
        $event = $this->getConfig("app.event");

        if (is_subclass_of($event, BaseEvent::class)) {
            $this->event = new $event;
        } else {
            $output = new ConsoleOutput();
            $output->writeln('config "app.event" value is instance \Melon\Events\BaseEvent');
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
