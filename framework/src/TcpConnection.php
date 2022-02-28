<?php

namespace Melon;

class TcpConnection
{
    public function __construct(private readonly mixed $conn, string $remote_address = '')
    {
        dd($this->conn, $remote_address);
    }
}
