<?php

namespace App\Core\Exceptions;

use RuntimeException;
use Throwable;

class AuthException extends RuntimeException
{
    private string $type;

    public function __construct(string $type, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $this->type = $type;
        parent::__construct($message, $code, $previous);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
