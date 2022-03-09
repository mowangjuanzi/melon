<?php

namespace Melon\Http;

use Melon\Foundation\Application;
use Melon\Http\Enums\ResponseTypeEnum;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TcpConnection
{
    public function __construct(private readonly mixed $conn, private readonly string $remote_address = '')
    {

    }

    public function execute()
    {
        $request = new Request($this->conn, $this->remote_address);

        // 执行路由解析
        $action = Application::getInstance()->routing->dispatch($request->enumMethod(), $request->path());

        // 目前只支持两种类型，一种是控制器，一种是静态文件
        if ($action['type'] == ResponseTypeEnum::CONTROLLER) {
            $controller = new $action['action'][0];
            /** @var Response $response */
            $response = $controller->{$action['action'][1]}();
        } else {
            $response = (new Response())->file(Application::getInstance()->publicPath($request->path()));
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
