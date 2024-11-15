<?php

use OpenTelemetry\API\Trace\TracerInterface;
use Illuminate\Support\Facades\DB;

function withSpan(TracerInterface $tracer, string $spanName, array $attributes = [], \Closure $callback)
{
    // Start a new span
    $span = $tracer->spanBuilder($spanName)->startSpan();
    $scope = $span->activate();

    try {
        // Set any attributes on the span
        foreach ($attributes as $key => $value) {
            $span->setAttribute($key, $value);
        }

        // Execute the callback (the actual business logic)
        return $callback($span);
    } catch (\Exception $e) {
        $span->recordException($e);
        throw $e;
    } finally {
        // End the span and detach the scope
        $span->end();
        $scope->detach();
    }
}
