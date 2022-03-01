<?php

namespace Melon;

class Response
{
    public function __construct(protected string $content = '')
    {

    }

    /**
     * 发送内容
     * @param mixed $conn
     * @return void
     */
    public function send(mixed $conn)
    {
        $body_len = strlen($this->content);

        $response = $this->content;

        // 写入 header
        stream_socket_sendto($conn, "HTTP/1.1 200 OK\r\nServer: Melon\r\nConnection: keep-alive\r\nContent-Type: text/html;charset=utf-8\r\nContent-Length: $body_len\r\n\r\n$response");
    }
}
