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

        // 执行路由解析
        $action = Application::getInstance()->routing->dispatch($request->enumMethod(), $request->path());

        $controller = new $action['action'][0];

        $response = $controller->{$action['action'][1]}();

        // 将返回的资源进行写入
        $response->send($this->conn);

        $this->application->event->remove($this->conn, EventEnum::READ);

        stream_socket_shutdown($this->conn, STREAM_SHUT_WR);
    }
}
