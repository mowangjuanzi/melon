<?php

namespace Melon\Http\Enums;

enum HttpMethodEnum
{
    case GET;
    case HEAD;
    case POST;
    case PUT;
    case DELETE;
    case CONNECT;
    case OPTIONS;
    case TRACE;
    case PATCH;
    case NONE;

    /**
     * match http method
     * @param string $method
     * @return HttpMethodEnum
     */
    public static function match(string $method): HttpMethodEnum
    {
        return match (strtoupper($method)) {
            self::GET->name => self::GET,
            self::HEAD->name => self::HEAD,
            self::POST->name => self::POST,
            self::PUT->name => self::PUT,
            self::DELETE->name => self::DELETE,
            self::CONNECT->name => self::CONNECT,
            self::OPTIONS->name => self::OPTIONS,
            self::TRACE->name => self::TRACE,
            self::PATCH->name => self::PATCH,
            default => self::NONE,
        };
    }
}
