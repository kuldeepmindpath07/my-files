<?php

use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

// Environment setup
putenv('OTEL_PHP_FIBERS_ENABLED=true');
putenv('OTEL_SERVICE_NAME=MyServiceName');

// OpenTelemetry transport and tracing setup
$httpTransport = (new OtlpHttpTransportFactory())->create('http://localhost:4318/v1/traces', 'application/json');
$exporter = new SpanExporter($httpTransport);
$tracerProvider = new TracerProvider(new SimpleSpanProcessor($exporter));
$tracer = $tracerProvider->getTracer('MyServiceName');

// Slim app setup
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

// Loading routes from separate files
foreach (['home', 'contact', 'contact_child'] as $route) {
    (require __DIR__ . "/routes/{$route}.php")($app, $tracer);
}

// Run the Slim app
$app->run();
