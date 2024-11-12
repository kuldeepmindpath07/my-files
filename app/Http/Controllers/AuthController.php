<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use OpenTelemetry\API\Trace\TracerInterface;

class AuthController extends Controller
{
    protected TracerInterface $tracer;
    public function __construct(TracerInterface $tracer)
    {
        error_log("1 heere");
        $this->tracer = $tracer;
    }

    // public function index()
    // {
    //     $span = $this->tracer->spanBuilder('view.login')->startSpan();
    //     $scope = $span->activate();

    //     try {
    //         return view('auth.login');
    //     } finally {
    //         $span->end();
    //         $scope->detach();
    //     }
    // }

    public function register_view()
    {
        $span = $this->tracer->spanBuilder('view.register')->startSpan();
        $scope = $span->activate();
        try {
            error_log("here 2");
            return view('auth.register');
        } finally { 
            $span->end();
            $scope->detach();
            error_log("here 3");
        }
    }

    public function login(Request $request)
    {
        // $span = $this->tracer->spanBuilder('action.login')->startSpan();
        // $scope = $span->activate();

        try {
            // Handle login logic here
            // For example, dd($request->all()) for testing
            dd($request->all());
        } finally {
            // $span->end();
            // $scope->detach();
        }
    }

    public function register(Request $request)
    {
        $span = $this->tracer->spanBuilder('action.register')->startSpan();
        $scope = $span->activate();

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
            ]);

            $span->setAttribute('user.name', $request->name);
            $span->setAttribute('user.email', $request->email);
            $span->addEvent('User registered');

            // Redirect or return success response
            return redirect()->route('login')->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            $span->recordException($e);
            throw $e;
        } finally {
            $span->end();
            $scope->detach();
        }
    }
}
