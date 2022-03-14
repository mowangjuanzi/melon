<?php

namespace Melon\Foundation\Exception;

use Melon\Http\TcpConnection;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler
{
    /**
     * @param Throwable $throwable
     * @param TcpConnection $connection
     * @return void
     */
    public function render(Throwable $throwable, TcpConnection $connection)
    {
        $response = response($throwable->getMessage(), $throwable->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);

        $connection->send($response);
    }
}
