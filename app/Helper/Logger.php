<?php

declare(strict_types=1);

namespace App\Helper;

// TODO: Trait used to write logs all over the code.
use App\Enums\HttpErrorCodes;

trait Logger
{
    public function log(
        string $message,
        int $code = HttpErrorCodes::HTTP_INTERNAL_SERVER_ERROR->value,
        array $context = []
    ): void {
        // TODO: Write a proper log, json formatted.
    }
}
