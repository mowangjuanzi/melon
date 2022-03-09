<?php


namespace Melon\Foundation;


use FilesystemIterator;
use LogicException;
use Melon\Collections\Arr;
use Melon\Container\Container;
use Melon\Events\EventServiceProvider;
use Melon\Log\LogServiceProvider;
use Melon\Routing\Routing;
use Melon\Routing\RoutingServiceProvider;
use Melon\Support\ServiceProvider;
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
     * Indicates if the application has "booted".
     *
     * @var bool
     */
    protected bool $booted = false;

    /**
     * All the registered service providers.
     *
     * @var ServiceProvider[]
     */
    protected array $serviceProviders = [];

    /**
     * The names of the loaded service providers.
     *
     * @var array
     */
    protected array $loadedProviders = [];

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
        $this->registerBaseServiceProviders();
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
     * Register all the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * Register a service provider with the application.
     *
     * @param ServiceProvider $provider
     * @param bool $force
     * @return ServiceProvider
     */
    public function register(ServiceProvider $provider, bool $force = false): ServiceProvider
    {
        if (($registered = $this->getProvider($provider)) && ! $force) {
            return $registered;
        }

        $provider->register();

        // If there are bindings / singletons set as properties on the provider we
        // will spin through them and register them with the application, which
        // serves as a convenience layer while registering a lot of bindings.
        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->markAsRegistered($provider);

        // If the application has already booted, we will call this boot method on
        // the provider class, so it has an opportunity to do its boot logic and
        // will be ready for any usage by this developer's application logic.
        if ($this->isBooted()) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param ServiceProvider $provider
     * @return ServiceProvider|null
     */
    public function getProvider(ServiceProvider $provider): ?ServiceProvider
    {
        return array_values($this->getProviders($provider))[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param ServiceProvider $provider
     * @return array
     */
    public function getProviders(ServiceProvider $provider): array
    {
        $name = get_class($provider);

        return Arr::where($this->serviceProviders, function ($value) use ($name) {
            return $value instanceof $name;
        });
    }

    /**
     * Mark the given provider as registered.
     *
     * @param ServiceProvider $provider
     * @return void
     */
    protected function markAsRegistered(ServiceProvider $provider)
    {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[get_class($provider)] = true;
    }

    /**
     * Boot the given service provider.
     *
     * @param  ServiceProvider  $provider
     * @return void
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        $provider->callBootingCallbacks();

        if (method_exists($provider, 'boot')) {
            $this->call([$provider, 'boot']);
        }

        $provider->callBootedCallbacks();
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
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
     * Get the path to the resources' directory.
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
