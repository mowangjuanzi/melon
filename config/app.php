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
];
