<?php


namespace Melon\Foundation;


use FilesystemIterator;
use Melon\Http\Console\ServerStartCommand;
use Melon\Routing\Routing;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use RecursiveDirectoryIterator;
use Revolt\EventLoop;
use Revolt\EventLoop\Driver;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * shared application.
     * @var Application
     */
    protected static Application $instance;

    /**
     * routing collection.
     * @var Routing
     */
    public readonly Routing $routing;

    /**
     * logging.
     * @var Logger
     */
    protected readonly Logger $logger;

    /**
     * version number.
     */
    public const VERSION = "0.0.2";

    /**
     * application name.
     */
    public const NAME = "melon";

    /**
     * base path.
     * @var string
     */
    protected string $basePath = '';

    /**
     * config.
     * @var array
     */
    protected array $config = [];

    /**
     * buildin command array.
     * @var array|string[]
     */
    protected array $commands = [
        ServerStartCommand::class,
    ];

    /**
     * constructor.
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;

        parent::__construct("melon", self::VERSION);

        $this->setInstance();

        $this->loadConfig();

        $this->registerCommands();

        $this->initLogger();

        $this->registerEvent();

        $this->loadRouting();

        cli_set_process_title(sprintf("%s main process", self::NAME));
    }

    /**
     * load config.
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
     * fetch config.
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
     * register commands.
     */
    protected function registerCommands()
    {
        foreach ($this->commands as $item) {
            $this->add(new $item);
        }
    }

    /**
     * register event loop.
     * @return void
     */
    protected function registerEvent()
    {
        $event = $this->getConfig("app.event");
        if ($event instanceof Driver) {
            EventLoop::setDriver(new $event);
        }
    }

    /**
     * route is instantiated and load config file.
     * @return void
     */
    protected function loadRouting()
    {
        $this->routing = new Routing();

        $this->routing->loadStaticResponse($this->basePath . "/public");

        $func = function ($routing) {
            require $this->basePath . "/routes/web.php";
        };

        $func($this->routing);
    }

    /**
     * init logger.
     * @return void
     */
    protected function initLogger()
    {
        $this->logger = new Logger(self::NAME);

        $logging = $this->getConfig("logging");

        $handler = new RotatingFileHandler($logging['path'], 2,  $logging['level'], true, 0622);

        $this->logger->pushHandler($handler);
    }

    /**
     * set shared instance.
     * @return bool
     */
    protected function setInstance(): bool
    {
        self::$instance = $this;
        return true;
    }

    /**
     * get shared instance.
     * @return Application
     */
    public static function getInstance(): Application
    {
        return self::$instance;
    }

    /**
     * get absolute path.
     * @param string $path
     * @return string
     */
    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get the path to the public directory.
     *
     * @param string $path
     * @return string
     */
    public function publicPath(string $path = ''): string
    {
        return $this->basePath("public") . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * get the path to storage directory.
     * @param string $path
     * @return string
     */
    public function storagePath(string $path = ''): string
    {
        return $this->basePath("storage") . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }
}
