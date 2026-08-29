<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class InvalidRequestException extends \Exception
{
    private const MESASGE = "Invalid request";

    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(self::MESASGE, $code, $previous);
    }
}
