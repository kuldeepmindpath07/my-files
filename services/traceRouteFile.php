<?php

namespace App\Services;

use OpenTelemetry\API\Trace\TracerInterface;
use Illuminate\Support\Facades\Log;

function traceRoute(string $name, string $route, string $method, callable $handler, TracerInterface $tracer)
{
    $span = $tracer->spanBuilder("route-$name-$method")->startSpan();
    $scope = $span->activate();

    try {
        $span->setAttribute('http.route', $route);
        $span->setAttribute('http.method', strtoupper($method));
        return $handler();
    } finally {
        $span->end();
        $scope->detach();
    }
}
