<?php

declare(strict_types=1);

namespace App\Helper;

use App\Enums\HttpCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait Logger
{
    /**
     * This method is created in order to facilitate an option to provide more context to a log.
     * For current usages, only the IP is fetched from the request.
     */
    public function buildContext(
        Request $request,
        array   $context = [],
    ): array {
        return [
            'context' => $context,
            'request-details' => [
                'client_ip'  => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id'    => $request->user()?->id ?? "N/A",
            ]
        ];
    }

    private function prepareContext(
        int $code,
        array $context = [],
    ): array
    {
        return [
            'code' => $code,
            'specific-information' => $context,
        ];
    }

    public function log(
        string $message,
        Request $request,
        int $code = HttpCodes::HTTP_INTERNAL_SERVER_ERROR->value,
        array $context = [],
        string $level = 'info',
    ): void {
        Log::log(
            $level,
            $message,
            $this->buildContext(
                $request,
                $this->prepareContext($code, $context)
            )
        );
    }
}
