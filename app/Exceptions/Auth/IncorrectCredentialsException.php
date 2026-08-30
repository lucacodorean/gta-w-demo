<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use App\Enums\HttpCodes;
use InvalidArgumentException;

class IncorrectCredentialsException extends InvalidArgumentException
{
    private const MESSAGE = "Invalid credentials";

    public function __construct() {
        parent::__construct(self::MESSAGE, HttpCodes::HTTP_UNAUTHORIZED->value);
    }
}
