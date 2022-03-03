<?php

use Melon\Events\SelectEvent;

return [

    /**
     * 监听地址 host:port
     */
    "listen" => '127.0.0.1:7998',

    /**
     * 公共目录
     */
    "static" => "public",

    /**
     * 是否开启后台
     */
    "daemon" => false,

    /**
     * 事件
     */
    "event" => SelectEvent::class,

    /**
     * 静态文件资源缓存时间，单位为秒
     *
     * 如果为 0 表示不缓存，如果为复数，则重置为 0
     *
     * 默认值：86400
     */
    "static_cache" => 86400,
];
