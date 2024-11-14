<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use OpenTelemetry\API\Trace\TracerInterface;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register_view()
    {
        error_log("coming here");
        return view('auth.register');
    }

    public function login_view(Request $request)
    {
        error_log("coming heresds");

        error_log(print_r($request->all(), true));
        return view('auth.login');
    }

    public function register(Request $request, TracerInterface $tracer)
    {
        // Start a specific span for registration action
        $span = $tracer->spanBuilder('register-action')->startSpan();
        $scope = $span->activate();

        try {
            $span->setAttribute('user.name', $request->name);
            $span->setAttribute('user.email', $request->email);

            // Log database query to span
            DB::enableQueryLog();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
            ]);

            $queries = DB::getQueryLog();
            $span->setAttribute('db.query', json_encode($queries));
            $span->addEvent('User registered successfully');

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
