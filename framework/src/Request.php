<?php

namespace Melon;

use Melon\Enums\HttpMethod;

class Request
{
    private readonly HttpMethod $method;

    private readonly string $uri;

    private readonly array $query;

    private readonly array $header;

    public function __construct(private readonly mixed $conn, private readonly string $remote_address = '')
    {
        $line = stream_get_line($this->conn, 2048, "\n");

        $line = explode(" ", $line);

        // 获取 http method
        $this->method = match (strtoupper($line[0])) {
            HttpMethod::GET->name => HttpMethod::GET,
            HttpMethod::POST->name => HttpMethod::POST,
        };

        // 获取 path 和 query
        $line[2] = parse_url($line[1]);
        $this->uri = $line[2]['path'] ?? '/';
        parse_str($line[2]['query'] ?? '', $query);
        $this->query = $query ?: [];

        // 获取 header
        while ($conn = stream_get_line($this->conn, 2048, "\r\n")) {
            $conn = explode(": ", $conn);
            $this->header[$conn[0]] = $conn[1];
        }

        dd($this);
    }

    public function query()
    {

    }

    public function post()
    {

    }

    public function method(): string
    {
        return $this->method->name;
    }
}
