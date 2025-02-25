<?php

declare(strict_types=1);

return [
    'short' => parse_url(env('APP_URL'), PHP_URL_HOST),
    'protocol' => parse_url(env('APP_URL'), PHP_URL_SCHEME),
];
