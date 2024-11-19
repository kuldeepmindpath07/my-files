<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\Context\Context;

class LogRequestDetails
{
    public function handle(Request $request, Closure $next)
    {
        // Proceed with the next middleware and get the response
        $response = $next($request);

        // Prepare log data
        $logData = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->status(),
            'endpoint' => $request->route()?->uri(),
        ];

        // Retrieve the active span from the current context
        $activeSpan = Span::fromContext(Context::getCurrent());
        
        // Check if there is an active span
        if ($activeSpan->isRecording()) {
            $spanContext = $activeSpan->getContext(); // Get the span's context
            $traceId = $spanContext->getTraceId();    // Extract the traceId
            $spanId = $spanContext->getSpanId();      // Extract the spanId

            // Add traceId and spanId to log data
            $logData['traceId'] = $traceId;
            $logData['spanId'] = $spanId;
        } else {
            // Handle cases where no span is active
            $logData['traceId'] = 'N/A';
            $logData['spanId'] = 'N/A';
        }

        // Log the request details with traceId and spanId
        Log::info('Request Log', $logData);

        return $response;
    }
}
