<?php

namespace Melon\Events;

use Closure;
use Melon\Enums\EventEnum;
use Melon\TcpConnection;

abstract class BaseEvent
{
    /**
     * 接受链接
     * @param $stream
     * @return void
     */
    public function acceptConnection($stream)
    {
        $conn = stream_socket_accept($stream, 0, $remote_address);

        if (!$conn) {
            return ;
        }

        stream_set_blocking($stream, false);

        $tcp = new TcpConnection($conn, $remote_address);
    }

    public abstract function add(mixed $stream, EventEnum $eventEnum, Closure $callback = null);
}
