<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Helper\Logger;
use Illuminate\Routing\Controllers\Middleware;

class EnsureNotSessionActiveMiddleware extends Middleware
{
    use Logger;

    public function __invoke()
    {
        // TODO: Validate that an already connected user can't access login and register routes.
        // TODO: Log the failure situation. Issue a notification as well.
    }
}
