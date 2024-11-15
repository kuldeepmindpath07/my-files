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
    // Call the wrapper function with the desired span name and attributes
    return withSpan($tracer, 'register-action', [
        'user.name' => $request->name,
        'user.email' => $request->email
    ], function ($span) use ($request) {
        // Log database query to span
        DB::enableQueryLog();

        // Perform the registration logic
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
        ]);

        // Get the database queries and set them as span attributes
        $queries = DB::getQueryLog();
        $span->setAttribute('db.query', json_encode($queries));
        $span->addEvent('User registered successfully');

        return redirect()->route('login')->with('success', 'Registration successful!');
    });
}

}
