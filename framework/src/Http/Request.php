<?php

namespace Melon\Http;

use Melon\Foundation\Application;
use Melon\Http\Enums\HttpMethodEnum;

class Request
{
    private readonly HttpMethodEnum $method;

    private readonly string $uri;

    private readonly array $query;

    private bool $booted = false;

    /**
     * 客户端发来的 header
     * @var array|string[]
     */
    private array $header = [];

    /**
     * 服务器相关 header
     * @var array|string[]
     */
    private array $server = [
        "server_software" => "Melon/" . Application::VERSION,
    ];

    /**
     * 实例化
     * @param resource $conn
     * @param string $remote_address
     */
    public function __construct(private readonly mixed $conn, string $remote_address = '')
    {
        $line = stream_get_line($this->conn, 2048, "\r\n");

        if (!is_string($line)) {
            return ;
        }

        $line = explode(" ", trim($line));

        // 获取 http method
        $this->method = HttpMethodEnum::match($line[0]);

        // 获取 path 和 query
        $line[2] = parse_url($line[1]);
        $this->uri = $line[2]['path'] ?? '/';
        parse_str($line[2]['query'] ?? '', $query);
        $this->query = $query ?: [];

        // 获取 header
        while ($conn = stream_get_line($this->conn, 2048, "\r\n")) {
            $conn = explode(": ", $conn);
            $this->header[strtolower($conn[0])] = $conn[1];
        }

        // 格式化 request_id
        $this->parseRequestId();

        // 解析远程地址
        $remote_address = parse_url($remote_address);
        $this->server["remote_host"] = $remote_address['host'];
        $this->server['remote_port'] = $remote_address['port'];

        $this->booted = true;
    }

    /**
     * GET 获取
     * @param string $prefix
     * @param string|array|null $default
     * @return string|array|null
     */
    public function query(string $prefix, string|array|null $default = null): string|array|null
    {
        return $this->query[$prefix] ?: $default;
    }

    /**
     * POST 获取
     * @return void
     */
    public function post()
    {

    }

    public function path(): string
    {
        return $this->uri;
    }

    public function method(): string
    {
        return $this->enumMethod()->name;
    }

    public function enumMethod(): HttpMethodEnum
    {
        return $this->method;
    }

    /**
     * 获取 header
     * @param string $key
     * @return ?string
     */
    public function header(string $key): ?string
    {
        return $this->header[strtolower($key)] ?? null;
    }

    /**
     * 获取 server
     * @param string $key
     * @return ?string
     */
    public function server(string $key): ?string
    {
        return $this->server[strtolower($key)] ?? null;
    }

    /**
     * 格式化 request_id
     * @return void
     */
    public function parseRequestId()
    {
        if (empty($this->server('request_id'))) {
            $this->server['request_id'] = sprintf("%08x%08x%08x%08x", mt_rand(), mt_rand(), mt_rand(), mt_rand());
        }
    }

    /**
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }
}
