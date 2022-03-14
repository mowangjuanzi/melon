<?php

namespace Melon\Http;

use App\Exceptions\Handler;
use Melon\Foundation\Application;
use Melon\Http\Enums\ResponseTypeEnum;
use Revolt\EventLoop;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class TcpConnection
{
    public function __construct(private readonly mixed $conn, private readonly string $remote_address = '')
    {
        // set error|exception handler
        EventLoop::setErrorHandler(function (\Throwable $throwable) {
            /** @var Handler $handler */
            $handler = app(Handler::class);
            $handler->render($throwable, $this);
        });
    }

    public function execute()
    {
        $request = new Request($this->conn, $this->remote_address);

        if (!$request->isBooted()) {
            stream_socket_shutdown($this->conn, STREAM_SHUT_WR);
            fclose($this->conn);
            return ;
        }

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

        $this->send($response);
    }

    /**
     * send header and data
     * @param SymfonyResponse $response
     * @return void
     */
    public function send(SymfonyResponse $response)
    {
        EventLoop::onWritable($this->conn, function ($watcher, $conn) use($response) {
            stream_socket_sendto($conn, $response);

            // 对返回资源进行处理
            if ($response instanceof BinaryFileResponse) {
                $tmp = fopen($response->getFile()->getPathname(), 'r');
                stream_copy_to_stream($tmp, $conn);
                fclose($tmp);
            }

            stream_socket_shutdown($conn, STREAM_SHUT_WR);
            fclose($conn);
            EventLoop::cancel($watcher);
        });
    }
}
