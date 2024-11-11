<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\StatusCode;

class AuthController extends Controller
{
    private $tracer;

    public function __construct()
    {
        // Directly instantiate the tracer without using TracerInterface
        $httpTransport = (new OtlpHttpTransportFactory())->create('http://localhost:4318/v1/traces', 'application/json');
        $exporter = new SpanExporter($httpTransport);
        $tracerProvider = new TracerProvider(new SimpleSpanProcessor($exporter));
        $this->tracer = $tracerProvider->getTracer('LaravelService');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function register_view()
    {
        $span = $this->tracer->spanBuilder('register_view')->startSpan();
        $scope = $span->activate();

        try {
            return view('auth.register');
        } catch (\Exception $e) {
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;
        } finally {
            $span->end();
            $scope->detach();
        }
    }

    public function login(Request $request)
    {
        dd($request->all());
    }

    public function register(Request $request)
    {
        $span = $this->tracer->spanBuilder('register_action')->startSpan();
        $scope = $span->activate();

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
            ]);

            // Set status to OK for the trace
            $span->setStatus(StatusCode::STATUS_OK);

            return redirect()->route('login')->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            // Set status to ERROR if something goes wrong
            $span->setStatus(StatusCode::STATUS_ERROR, $e->getMessage());
            throw $e;
        } finally {
            $span->end();
            $scope->detach();
        }
    }
}
