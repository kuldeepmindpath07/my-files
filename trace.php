<?php

use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

// Enable OpenTelemetry fibers
putenv('OTEL_PHP_FIBERS_ENABLED=true');

putenv('OTEL_SERVICE_NAME=myLaravelService');

// Set up the OpenTelemetry HTTP transport
$httpTransport = (new OtlpHttpTransportFactory())
    ->create('http://192.168.1.196:4318/v1/traces', 'application/json'); // Replace with your OTLP endpoint

// Set up the SpanExporter and TracerProvider
$exporter = new SpanExporter($httpTransport);
$tracerProvider = new TracerProvider(
    new SimpleSpanProcessor($exporter)
);
$tracer = $tracerProvider->getTracer('demo');

// Create the Slim application
$app = AppFactory::create();

// Add error handling middleware
$app->addErrorMiddleware(true, true, true);

// Log incoming requests
$app->add(function (Request $request, $handler) {
    error_log("Requested URI: " . $request->getUri());
    return $handler->handle($request);
});

// Home route
$app->get('/', function (Request $request, Response $response) use ($tracer) {
    // Start a new span for the request
    $span = $tracer->spanBuilder('/myHome')->startSpan();

    // Set attributes for the HTTP method and URL path
    $span->setAttribute('http.method', $request->getMethod());
    $span->setAttribute('http.url', (string)$request->getUri()->getPath());

    // Call an external API (example API URL)
    $apiUrl = 'https://fakestoreapi.com/products'; // Replace with your API endpoint
    $data = json_decode(file_get_contents($apiUrl), true); // Fetch and decode JSON data

    // Prepare the HTML response for the home page
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home</title>
    </head>
    <body>
        <h1>Data from API</h1>';

    if (is_array($data) && count($data) > 0) {
        $html .= '<ul>';
        foreach ($data as $item) {
            // Adjust keys based on your API response
            $html .= '<li>' . htmlspecialchars($item['title']) . ' - ' . htmlspecialchars($item['price']) . '</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= '<p>No data available.</p>';
    }

    // Add navigation button to the contact page
    $html .= '<button onclick="location.href=\'/contact\'">Go to Contact Page</button>';

    $html .= '</body></html>';
    $response->getBody()->write($html);

    // End the span
    $span->end();

    return $response;
});

// Contact route
$app->get('/contact', function (Request $request, Response $response) use ($tracer) {
    // Start a new span for the request
    $span = $tracer->spanBuilder('/mycontact')->startSpan();

    // Set attributes for the HTTP method and URL path
    $span->setAttribute('http.method', $request->getMethod());
    $span->setAttribute('http.url', (string)$request->getUri()->getPath());

    // Prepare the HTML response for the contact page
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact</title>
    </head>
    <body>
        <div>Header</div>
        <div>This is the contact page</div>
        <div>Footer</div>

        <!-- Add navigation button to the contact child route -->
        <button onclick="location.href=\'/contact/child\'">Contact us</button>
    </body>
    </html>';

    $response->getBody()->write($html);

    // End the span
    $span->end();

    return $response;
});

// Child route for /contact
$app->get('/contact/child', function (Request $request, Response $response) use ($tracer) {
    // Start a new span for the request
    $span = $tracer->spanBuilder('/mycontact/child')->startSpan();

    // Set attributes for the HTTP method and URL path
    $span->setAttribute('http.method', $request->getMethod());
    $span->setAttribute('http.url', (string)$request->getUri()->getPath());

    // Prepare the HTML response for the child route
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact us</title>
    </head>
    <body>
        <div>we will contact with you shortly</div>
    </body>
    </html>';

    $response->getBody()->write($html);

    // End the span
    $span->end();

    return $response;
});

// Run the application
$app->run();
