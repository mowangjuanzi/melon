<?php

use App\Console\ServerStartCommand;

return [

    /**
     * 是否开启后台
     */
    "daemon" => false,

    /**
     * 事件
     */
    "event" => [
        /**
         * 指定使用的模块
         */
        "use" => "select",
    ],

    /**
     * 内置命令
     */
    "commands" => [
        ServerStartCommand::class,
    ]
];
