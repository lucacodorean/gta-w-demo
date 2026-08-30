<?php

declare(strict_types=1);

namespace App\Helper;

use App\Enums\HttpCodes;
use App\Enums\LogEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait Logger
{
    /**
     * This method is created to facilitate an option to provide more context to a log.
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
                'timestamp'  => $request->timestamp,
            ]
        ];
    }

    private function prepareContext(
        LogEvents $event,
        int $code,
        array $context = [],
    ): array
    {
        return [
            'event' => $event->value,
            'code' => $code,
            'specific-information' => $context,
        ];
    }

    /**
     * The event carries its own wording (LogEvents::message()); its key travels
     * with the entry so logs can be filtered on the event rather than on text.
     */
    public function log(
        LogEvents $event,
        Request $request,
        int $code = HttpCodes::HTTP_INTERNAL_SERVER_ERROR->value,
        array $context = [],
        string $level = 'info',
    ): void {
        Log::log(
            $level,
            $event->message(),
            $this->buildContext(
                $request,
                $this->prepareContext($event, $code, $context)
            )
        );
    }

    public static function getEmptyContext(): array {
        return [];
    }
}
