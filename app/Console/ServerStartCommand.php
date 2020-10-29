<?php


namespace App\Console;


use Melon\Accept;
use Melon\Connections\TcpConnection;
use Melon\Events\Event;
use Melon\Interfaces\EventInterface;
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

        $address = "http://127.0.0.1:7998";
        $address = parse_url($address);

        // 设置 socket 选项
        $context = stream_context_create([
            "socket" => [
                "backlog" => 102400,
            ]
        ]);
//        stream_context_set_option($context, "socket", 'so_reuseport', 1);

        $main_socket = stream_socket_server("tcp://" . ($address['host'] ?? '127.0.0.1') . ":" . ($address['port'] ?? 7998));

        // 设置 keepalive 和 禁用 Nagle 算法
        $socket = socket_import_stream($main_socket);
        socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);
        socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);

        // 设置非堵塞
        stream_set_blocking($main_socket, false);

        $event = Event::getInstance();
        $event->add($main_socket, EventInterface::EVENT_READ, [$this, "acceptConnection"]);

        $event->loop();

        return self::SUCCESS;
    }

    public function acceptConnection($fd)
    {
        $socket = stream_socket_accept($fd, 0, $remote_address);

        $tcp_connection = new TcpConnection($socket, $remote_address);
    }
}
