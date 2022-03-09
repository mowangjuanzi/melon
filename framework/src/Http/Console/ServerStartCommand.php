<?php


namespace Melon\Http\Console;


use Melon\Console\Command;
use Melon\Http\TcpConnection;
use Revolt\EventLoop;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerStartCommand extends Command
{
    protected ?string $name = 'start';

    protected string $description = "start web server on cli mode";

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 设置 socket 选项
        $context = stream_context_create([
            "socket" => [
                "backlog" => 102400,
            ]
        ]);

        // SO_REUSEPORT https://www.cnblogs.com/anker/p/7076537.html
        stream_context_set_option($context, "socket", 'so_reuseport', 1);

        $config = app()->getConfig("app");

        $stream = stream_socket_server($config['listen'], $err_code, $err_message);

        $output->writeln("Visit http://{$config['listen']}/ in your browser.");

        // 设置 keepalive 和 禁用 Nagle 算法
//        $socket = socket_import_stream(stream);
//        socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
//        socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);

        // 设置非堵塞
        stream_set_blocking($stream, false);

        EventLoop::onReadable($stream, function ($watcher, $stream) {
            $conn = stream_socket_accept($stream, 0, $remote_address);

            if (!is_resource($conn) || @feof($conn)) {
                EventLoop::cancel($watcher);
            } else {
                $tcp = new TcpConnection($conn, $remote_address);
                $tcp->execute();
            }
        });

        EventLoop::run();

        return self::SUCCESS;
    }
}
