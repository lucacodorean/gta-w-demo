<?php

declare(strict_types=1);

namespace App\Enums;

enum HttpCodes: int
{

    case HTTP_OK = 200;
    case HTTP_CREATED = 201;
    case HTTP_REDIRECTED = 302;
    case HTTP_INTERNAL_SERVER_ERROR = 500;
    case HTTP_NOT_FOUND = 404;
    case HTTP_METHOD_NOT_ALLOWED = 405;
    case HTTP_UNPROCESSABLE_ENTITY = 422;
}
