<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Log;
use OpenTelemetry\API\Logs\LogRecord;
use OpenTelemetry\API\Logs\Severity;
use OpenTelemetry\Contrib\Otlp\LogsExporter;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\SDK\Common\Instrumentation\InstrumentationScopeFactory;
use OpenTelemetry\SDK\Logs\EventLoggerProvider;
use OpenTelemetry\SDK\Logs\LoggerProvider;
use OpenTelemetry\SDK\Logs\LogRecordLimitsBuilder;
use OpenTelemetry\SDK\Logs\Processor\SimpleLogRecordProcessor;

Route::get('/', function () {
    return view('welcome');
});

// Home route with OpenTelemetry logging
Route::get('/home', function () {
    error_log("ifslalalalal");
    // Initialize the OTLP transport and exporter
        $transport = (new OtlpHttpTransportFactory())->create('http://localhost:4318/v1/logs', 'application/json');
    
    $exporter = new LogsExporter($transport);
    
    // Set up the LoggerProvider and EventLogger
    $loggerProvider = new LoggerProvider(
        new SimpleLogRecordProcessor($exporter),
        new InstrumentationScopeFactory(
            (new LogRecordLimitsBuilder())->build()->getAttributeFactory()
        )
    );
    
    $eventLoggerProvider = new EventLoggerProvider($loggerProvider);
    
    
    $eventLogger = $eventLoggerProvider->getEventLogger('demo', '1.0', 'https://opentelemetry.io/schemas/1.7.1', ['foo' => 'bar']);
   
    // Log an event when the home route is accessed
    $eventLogger->emit(
        name: 'home-accessed',
        body: [
            // 'foo' => 'bar',
            // 'baz' => 'bat',
            // 'msg' => 'Hello world from the home route!',
            // 'randomNumber' => rand(1, 100),  // Random number for demonstration
        ],
        // timestamp: (new \DateTime())->getTimestamp() * LogRecord::NANOS_PER_SECOND,
        // severityNumber: Severity::INFO,
    );
    error_log("ifslalalaljhgfdsal");
    // Generate a random number and pass it to the view
    $randomNumber = rand(1, 100);  // Generate a random number between 1 and 100

    return view('home', compact('randomNumber')); // Pass the random number to the view
});

Route::get('login', [AuthController::class, 'login_view'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::get('register', [AuthController::class, 'register_view'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register');
