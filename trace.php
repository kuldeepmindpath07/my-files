<?php

use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\API\Trace\TracerInterface;

require __DIR__ . '/vendor/autoload.php';

// Environment setup
putenv('OTEL_PHP_FIBERS_ENABLED=true');
putenv('OTEL_SERVICE_NAME=LaravelService'); // Replace with your service name

// OpenTelemetry transport and tracing setup
$httpTransport = (new OtlpHttpTransportFactory())->create('http://localhost:4318/v1/traces', 'application/json');
$exporter = new SpanExporter($httpTransport);
$tracerProvider = new TracerProvider(new SimpleSpanProcessor($exporter));
$tracer = $tracerProvider->getTracer('LaravelService');

// Laravel application setup
$app = require_once __DIR__ . '/bootstrap/app.php';
error_log("once error");

// Register routes
$app->router->get('/register', ['uses' => 'App\Http\Controllers\AuthController@register_view']);
error_log("once error2");

$app->router->post('/register', function () use ($tracer) {
    error_log("once error3");
    
    // Start a new span for the /register route
    $span = $tracer->spanBuilder('register_route')->startSpan();
    $scope = $span->activate();

    try {
        // Forward the request to the controller
        $response = (new App\Http\Controllers\AuthController())->register(request());

        // Set span status
        $span->setStatus(\OpenTelemetry\SDK\Trace\StatusCode::STATUS_OK);
        return $response;
    } catch (\Exception $e) {
        // Log any exceptions to the trace
        $span->setStatus(\OpenTelemetry\SDK\Trace\StatusCode::STATUS_ERROR, $e->getMessage());
        throw $e;
    } finally {
        // End the span and detach the scope
        $span->end();
        $scope->detach();
    }
});

// Handle the HTTP request (let Laravel's kernel take care of it)
$response = $app->handle($request = Illuminate\Http\Request::capture());

// Send the response
$response->send();
