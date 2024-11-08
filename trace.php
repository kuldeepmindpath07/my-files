<?php

use Openelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Trace\SpanExporter; // Corrected namespace
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Validation\ValidationException;

require __DIR__ . '/vendor/autoload.php';

// Database configuration (assuming you have MySQL configured properly)
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'my_db_bro',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// OpenTelemetry transport and tracing setup
error_log("coming here");
$httpTransport = (new OtlpHttpTransportFactory())->create('http://localhost:4318/v1/traces', 'application/json');
error_log("nothinghre");
$exporter = new SpanExporter($httpTransport); // Corrected namespace
$tracerProvider = new TracerProvider(new SimpleSpanProcessor($exporter));
$tracer = $tracerProvider->getTracer('MyServiceName');

// Slim app setup
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

// Registration route
$app->post('/register', function ($request, $response) use ($tracer) {
    $span = $tracer->spanBuilder('/register')->startSpan();
    $span->setAttribute('http.method', $request->getMethod());
    $span->setAttribute('http.url', (string) $request->getUri()->getPath());

    // Get registration data from the request
    $data = $request->getParsedBody();
    
    // Validate input
    try {
        $validation = \Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        // Hash the password using PHP's built-in password_hash
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        // Create a new user
        \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $hashedPassword,
        ]);

        // Add success message to the response
        $response->getBody()->write('Registration successful!');
        $span->end();

        return $response;
    } catch (ValidationException $e) {
        // Handle validation failure
        $response->getBody()->write('Validation error: ' . $e->getMessage());
        $span->end();
        return $response->withStatus(400);
    } catch (\Exception $e) {
        // Handle general errors
        $response->getBody()->write('Error: ' . $e->getMessage());
        $span->end();
        return $response->withStatus(500);
    }
});

// Run the Slim app
$app->run();
