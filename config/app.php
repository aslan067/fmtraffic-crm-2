<?php
// Application configuration

declare(strict_types=1);

return [
    'app_name' => $_ENV['APP_NAME'] ?? 'FM CRM',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOL),
];
