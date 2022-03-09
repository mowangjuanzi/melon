<?php

namespace Melon\Foundation\Console;

use Melon\Foundation\Application;
use Melon\Http\Console\ServerStartCommand;
use Symfony\Component\Console\Application as SymfonyApplication;

class Kernel extends SymfonyApplication
{
    /**
     * build-in command array.
     * @var array|string[]
     */
    protected array $commands = [
        ServerStartCommand::class,
    ];

    public function __construct(protected Application $application)
    {
        parent::__construct(Application::NAME, Application::VERSION);

        $this->registerCommands();
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
}
