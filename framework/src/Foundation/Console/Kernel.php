<?php

namespace Melon\Foundation\Console;

use Melon\Console\Command;
use Melon\Foundation\Application;
use Melon\Http\Console\ServerStartCommand;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

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

    /**
     * Add a command to the console.
     *
     * @param SymfonyCommand $command
     * @return SymfonyCommand|null
     */
    public function add(SymfonyCommand $command): ?SymfonyCommand
    {
        if ($command instanceof Command) {
            $command->setMelon($this->application);
        }
        return parent::add($command);
    }
}
