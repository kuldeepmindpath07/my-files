<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\Span;

class AuthController extends Controller
{
    private $tracer;

    public function __construct()
    {
        $tracerProvider = new TracerProvider();
        $this->tracer = $tracerProvider->getTracer('register-tracer');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function register_view()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        dd($request->all());
    }

    public function register(Request $request)
    {
        $span = $this->tracer->spanBuilder('register_user')->startSpan();
        $scope = $span->activate();

        try {
            $span->setAttribute('user.name', $request->name);
            $span->setAttribute('user.email', $request->email);

            // Create the user
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
            ]);

            // $span->setStatus(\OpenTelemetry\SDK\Common\Attribute\StatusCode::STATUS_OK);
            return redirect()->route('login')->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            error_log("please give correct user's data");
        } finally {
            $span->end(); // End the span to capture trace data
            $scope->detach(); // Detach the span from the current context
        }
    }
}
