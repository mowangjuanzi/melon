<?php

namespace Melon;

use Melon\Enums\EventEnum;

class TcpConnection
{
    protected Application $application;

    public function __construct(private readonly mixed $conn, private readonly string $remote_address = '')
    {
        $this->application = Application::getInstance();

        stream_set_blocking($this->conn, false);

        $this->application->event->add($this->conn, EventEnum::READ, $this->execute(...));
    }

    public function execute()
    {
        $request = new Request($this->conn, $this->remote_address);
    }
}
