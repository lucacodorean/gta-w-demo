<?php

declare(strict_types=1);

namespace App\Enums;

use app\Exceptions\Auth\IncorrectCredentialsException;
use App\Exceptions\InvalidRequestException;

enum NotificationTypeEnum: string
{
    case LOGIN_INVALID_EMAIL_OR_PASSWORD = 'invalid_email_or_password';
    case REQUEST_INVALID = 'invalid_request';


    public function message(NotificationTypeEnum $type): string {
        return match($type) {
            self::LOGIN_INVALID_EMAIL_OR_PASSWORD =>
                (new IncorrectCredentialsException())->getMessage(),
            self::REQUEST_INVALID =>
                (new InvalidRequestException(code: HttpErrorCodes::HTTP_UNPROCESSABLE_ENTITY->value))->getMessage(),
        };
    }
}
