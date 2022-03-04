<?php

namespace Melon;

use Melon\Enums\EventEnum;
use Melon\Enums\ResponseTypeEnum;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

        $this->application->event->remove($this->conn, EventEnum::READ);

        // 执行路由解析
        $action = Application::getInstance()->routing->dispatch($request->enumMethod(), $request->path());

        // 目前只支持两种类型，一种是控制器，一种是静态文件
        if ($action['type'] == ResponseTypeEnum::CONTROLLER) {
            $controller = new $action['action'][0];
            /** @var Response $response */
            $response = $controller->{$action['action'][1]}();
        } else {
            $response = (new Response())->file($this->application->publicPath($request->path()));
        }

        stream_socket_sendto($this->conn, $response);

        // 对返回资源进行处理
        if ($response instanceof BinaryFileResponse) {
            $tmp = fopen($response->getFile()->getPathname(), 'r');
            stream_copy_to_stream($tmp, $this->conn);
            fclose($tmp);
        }

        stream_socket_shutdown($this->conn, STREAM_SHUT_WR);
    }
}
