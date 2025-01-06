<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    //this method will show our home page
    public function index(){
        $response = Http::get('https://fakestoreapi.com/products'); // Replace with your API URL

        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json(); // Get the response data as an array
        } else {
            $data = []; // Handle error accordingly
        }

        return view('home',compact('data'));
    }
    public function contact(){
        return view('app');
    }
}
