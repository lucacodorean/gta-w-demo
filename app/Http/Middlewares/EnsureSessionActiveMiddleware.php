<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Helper\Logger;
use App\Helper\NotificationSender;
use Illuminate\Routing\Controllers\Middleware;

class EnsureSessionActiveMiddleware extends Middleware
{
    use Logger, NotificationSender;

    public function __invoke()
    {
        // TODO: Validate that the session is active in order to pass this gate.
        // TODO: If the session is not active, ensure that a notification is sent
        // TODO: through the NotificationSender trait, and also that the action is logged.
    }
}

