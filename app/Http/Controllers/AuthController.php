<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function index(){
        return view('auth.login');
    }
    public function register_view(){
        return view('auth.register');
    }
    public function login(Request $request)
    {
        dd($request->all());
    }
    public function register(Request $request)
{

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Hash::make($request->password),
    ]);

    // Redirect or return success response
    return redirect()->route('login')->with('success', 'Registration successful!');
}
}