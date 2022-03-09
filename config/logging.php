<?php

use Monolog\Logger;

return [
    "level" => Logger::DEBUG,
    "driver" => "single", // 目前仅支持单个文件
    "path" => storage_path("logs/app.log"),
];
