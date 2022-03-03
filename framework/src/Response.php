<?php

namespace Melon;

use SplFileInfo;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    public function __construct(?string $content = '', int $status = 200, array $headers = [])
    {
        parent::__construct($content, $status, $headers);

        $this->headers->set("Server", "Melon/" . Application::VERSION);
        $this->headers->set("Content-Length", strlen($content));
    }

    /**
     * 资源响应
     * @param SplFileInfo $file
     * @param resource $stream
     * @return Response
     */
    public function file(SplFileInfo $file, mixed $stream): static
    {
        if ($file->isReadable()) {
            $charset = $this->charset ?: 'UTF-8';
            $this->headers->set("Content-Length", $file->getSize());
            $this->headers->set('Content-Type', mime_content_type($file->getPathname()) . '; charset=' . $charset);
        } elseif ($file->isFile()) {
            $this->setStatusCode(403);
        } else {
            $this->setStatusCode(404);
        }

        stream_socket_sendto($stream, $this->__toString());

        if ($file->isReadable()) {
            stream_copy_to_stream(fopen($file->getPathname(), 'r'), $stream);
        }

        return $this;
    }
}
