<?php

namespace Melon;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param string $file
     * @param array $headers
     * @return BinaryFileResponse
     */
    public function file(string $file, array $headers = []): BinaryFileResponse
    {
        return new BinaryFileResponse($file, 200, $headers);
    }

    /**
     * Json 响应
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return JsonResponse
     */
    public function json(array|object $data = [], int $status = 200, array $headers = [], int $options = 0): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $options);
    }
}
