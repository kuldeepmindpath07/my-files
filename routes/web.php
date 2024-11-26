<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrometheusController;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

// Create a shared registry instance
$sharedRegistry = new CollectorRegistry(new InMemory());

// Bind the shared registry to the service container for reuse
app()->instance(CollectorRegistry::class, $sharedRegistry);

Route::get('/', function () use ($sharedRegistry) {
    // Define a counter metric for tracking requests to the welcome page
    $counter = $sharedRegistry->registerCounter(
        'app',               // Namespace
        'welcome_requests',  // Metric name
        'Total requests to the welcome page', // Help text
        ['method']           // Labels
    );

    // Increment the counter with method as a label
    $counter->inc([request()->method()]);

    return view('welcome');
});

Route::get('metrics', [PrometheusController::class, 'metrics'])->name('metrics');

// Login and Register routes
Route::get('login', [AuthController::class, 'login_view'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::get('register', [AuthController::class, 'register_view'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register');
