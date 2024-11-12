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
        return view('auth.register');
    }

    public function login(Request $request)
    {
        // Handle login logic here
        error_log(print_r($request->all(), true));  // Log the request data instead of dumping
        // Optionally use dd('Request received') to confirm the request is being processed
         // Remove this after confirming it's working
         return view('auth.login');
    }


    public function register(Request $request, TracerInterface $tracer)
    {
    // Start the span for the registration process
    $span = $tracer->spanBuilder('register-action')->startSpan();
    $scope = $span->activate();

    try {
        // Create the user
        $span->setAttribute('user.name', $request->name);
        $span->setAttribute('user.email', $request->email);

        // Optionally, add the database query to the span
        DB::enableQueryLog(); // Enable query logging to capture the DB queries

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
        ]);

        // After the user is created, capture the database query logs
        $queries = DB::getQueryLog();
        $span->setAttribute('db.query', json_encode($queries));

        // Add event to the span
        $span->addEvent('User registered successfully');

        // You can also add any other relevant data to the span, such as HTTP method and route
        $span->setAttribute('http.method', $request->method());
        $span->setAttribute('http.route', 'register');

        // Redirect to the login page with success
        return redirect()->route('login')->with('success', 'Registration successful!');
    } catch (\Exception $e) {
        // If there’s an error, capture the exception in the span
        $span->recordException($e);
        throw $e; // Re-throw the exception
    } finally {
        // End the span and detach the scope
        $span->end();
        $scope->detach();
    }
  }

}
