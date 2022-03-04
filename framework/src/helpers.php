<?php

use Melon\Response;

if (!function_exists("response")) {

    function response(?string $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}
