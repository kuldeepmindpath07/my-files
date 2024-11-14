<?php

namespace App\Http\Middleware;

use Closure;
use OpenTelemetry\API\Trace\TracerInterface;

class TraceMiddleware
{
    protected $tracer;

    // Inject TracerInterface into the constructor
    public function __construct(TracerInterface $tracer)
    {
        $this->tracer = $tracer;
    }

    public function handle($request, Closure $next)
    {
        // Start a span for the request
        $span = $this->tracer->spanBuilder('request')->startSpan();
        $scope = $span->activate();
        
        error_log("It's coming here");

        try {
            // Set attributes or events on the span if needed
            $span->setAttribute('http.method', $request->method());
            $span->setAttribute('http.route', $request->path());

            // Continue processing the request
            return $next($request);
        } finally {
            // End the span after the request completes
            $span->end();
            $scope->detach();
        }
    }
}
