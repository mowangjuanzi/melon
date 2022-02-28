<?php

namespace Melon\Events;

use Melon\TcpConnection;

class BaseEvent
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

        $tcp = new TcpConnection($conn, $remote_address);
    }
}
