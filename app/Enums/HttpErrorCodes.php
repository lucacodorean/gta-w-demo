<?php

declare(strict_types=1);

namespace App\Enums;

enum HttpErrorCodes: int
{
    case HTTP_INTERNAL_SERVER_ERROR = 500;
    case HTTP_NOT_FOUND = 404;
    case HTTP_METHOD_NOT_ALLOWED = 405;
    case HTTP_UNPROCESSABLE_ENTITY = 422;
}
