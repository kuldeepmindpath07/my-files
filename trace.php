<?php

// Include the Laravel autoload file to initialize the app
require __DIR__ . '/vendor/autoload.php'; // Correct path to vendor/autoload.php

// Initialize OpenTelemetry for tracing
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\StatusCode;
use OpenTelemetry\API\Trace\Tracer;
use OpenTelemetry\SDK\Trace\Span;

$httpTransport = (new OtlpHttpTransportFactory())->create('http://localhost:4318/v1/traces', 'application/json');
$exporter = new SpanExporter($httpTransport);
$tracerProvider = new TracerProvider(new SimpleSpanProcessor($exporter));
$tracer = $tracerProvider->getTracer('LaravelService');

// Home route
// Home route
function homePage($tracer) {
    $span = $tracer->spanBuilder('home_page')->startSpan();
    $scope = $span->activate();

    $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Home</title></head><body>';
    $html .= '<h1>Welcome to the Home Page</h1>';
    $html .= '<a href="/register">Go to Register</a>';
    $html .= '</body></html>';

    $span->end();
    $scope->detach();

    return $html;
}

// Register form route
function registerPage($tracer) {
    $span = $tracer->spanBuilder('register_view')->startSpan();
    $scope = $span->activate();

    $html = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Register</title></head><body>';
    $html .= '<h1>Register Page</h1>';
    $html .= '<form action="/register" method="POST">
                <input type="text" name="name" placeholder="Name">
                <input type="email" name="email" placeholder="Email">
                <input type="password" name="password" placeholder="Password">
                <button type="submit">Register</button>
              </form>';
    $html .= '</body></html>';

    $span->end();
    $scope->detach();

    return $html;
}

// Database connection using mysqli
function handleRegistration($data, $tracer) {
    $span = $tracer->spanBuilder('kuldeep registration')->startSpan();
    $scope = $span->activate();

    try {
        // MySQL connection with mysqli
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "myDb";

        $conn = new mysqli($servername, $username, $password, $dbname);
        error_log("error here 1");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $data['name'], $data['email'], password_hash($data['password'], PASSWORD_DEFAULT));
        $stmt->execute();
        $conn->close();

        $span->setStatus(StatusCode::STATUS_OK);
        return 'Registration Successful!';
    } catch (\Exception $e) {
        // $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
        // throw $e;
        error_log("its causing error");
    } finally {
        $span->end();
        $scope->detach();
    }
}

// Run functions based on request (basic routing simulation)
if ($_SERVER['REQUEST_URI'] == '/register' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    echo registerPage($tracer);  // Pass tracer here
} elseif ($_SERVER['REQUEST_URI'] == '/register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    echo handleRegistration($_POST, $tracer);  // Pass tracer here as well
} else {
    echo homePage($tracer);  // Pass tracer here
}
