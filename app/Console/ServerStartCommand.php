<?php


namespace App\Console;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStartCommand extends Command
{
    protected static $defaultName = 'start';

    protected function configure()
    {
        $this->setDescription('启动Web服务器');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("开发中...");

        return self::SUCCESS;
    }
}
