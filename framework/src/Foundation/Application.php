<?php


namespace Melon\Foundation;


use FilesystemIterator;
use LogicException;
use Melon\Container\Container;
use Melon\Routing\Routing;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use RecursiveDirectoryIterator;
use Revolt\EventLoop;
use Revolt\EventLoop\Driver;

class Application extends Container
{
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
    protected readonly string $basePath;

    /**
     * config.
     * @var array
     */
    protected array $config = [];

    /**
     * constructor.
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->setBasePath($basePath);

        $this->registerBaseBindings();
        $this->registerCoreContainerAliases();

        $this->loadConfig();

        $this->initLogger();

        $this->registerEvent();

        $this->loadRouting();

        cli_set_process_title(sprintf("%s main process", self::NAME));
    }

    /**
     * Set the base path for the application.
     *
     * @param string $basePath
     * @return Application
     */
    public function setBasePath(string $basePath): static
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * Bind all the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.config', $this->configPath());
        $this->instance('path.public', $this->publicPath());
        $this->instance('path.storage', $this->storagePath());
        $this->instance('path.resources', $this->resourcePath());
        $this->instance('path.bootstrap', $this->bootstrapPath());
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
    protected function registerBaseBindings(): bool
    {
        static::setInstance($this);

        $this->instance('app', $this);

        return true;
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
     * Get the path to the bootstrap directory.
     *
     * @param string $path
     * @return string
     */
    public function bootstrapPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'bootstrap' . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get the path to the application configuration files.
     *
     * @param string $path
     * @return string
     */
    public function configPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config' . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @param string $path
     * @return string
     */
    public function path(string $path = ''): string
    {
        $appPath = $this->basePath . DIRECTORY_SEPARATOR . 'app';

        return $appPath . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get the path to the public directory.
     *
     * @param string $path
     * @return string
     */
    public function publicPath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public' . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Get the path to the resources directory.
     *
     * @param string $path
     * @return string
     */
    public function resourcePath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * get the path to storage directory.
     * @param string $path
     * @return string
     */
    public function storagePath(string $path = ''): string
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'storage' . ($path != '' ? DIRECTORY_SEPARATOR . $path : '');
    }

    /**
     * Register the core class aliases in the container.
     *
     * @return void
     */
    public function registerCoreContainerAliases()
    {
        foreach ([
                     "app" => [self::class],
                 ] as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    /**
     * Alias a type to a different name.
     *
     * @param string $abstract
     * @param string $alias
     * @return void
     *
     * @throws LogicException
     */
    public function alias(string $abstract, string $alias)
    {
        if ($alias === $abstract) {
            throw new LogicException("[$abstract] is aliased to itself.");
        }

        $this->aliases[$alias] = $abstract;

        $this->abstractAliases[$abstract][] = $alias;
    }
}
