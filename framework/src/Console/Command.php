<?php

namespace Melon\Console;

use Melon\Foundation\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var string
     */
    protected string $help = '';

    /**
     * The Melon application instance.
     *
     * @var Application
     */
    protected Application $melon;

    public function __construct()
    {
        parent::__construct($this->name);

        $this->setDescription($this->description);

        $this->setHelp($this->help);
    }

    /**
     * Set the Laravel application instance.
     * @return void
     */
    public function setMelon(Application $application)
    {
        $this->melon = $application;
    }
}
