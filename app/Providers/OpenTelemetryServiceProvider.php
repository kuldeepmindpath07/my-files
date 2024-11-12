<?php

namespace App\Providers;

use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use Illuminate\Support\ServiceProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\TracerProvider as SDKTracerProvider;

class OpenTelemetryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TracerInterface::class, function ($app) {
            $httpTransport = (new OtlpHttpTransportFactory())->create('http://localhost:4318/v1/traces', 'application/json');
            $exporter = new SpanExporter($httpTransport);
            // Create and return the OpenTelemetry tracer instance
            $tracerProvider = new SDKTracerProvider(new SimpleSpanProcessor($exporter));
            return $tracerProvider->getTracer('laravel-tracer');
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
