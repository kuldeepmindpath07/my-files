@extends('layouts.app')
@section('content')


<div class='container'>
<div class="row">
    <div class="card form-holder">
        <div class="card-body">
            <h1>Register</h1>
            @if(Session::has('errors'))
                <p class="text-danger">{{Session::get('errors')}}</p>
            @endif
            @if(Session::has('success'))
                <p class="text-success">{{Session::get('success')}}</p>
            @endif

            <form action="{{ route('register') }}" method="post">
                @csrf
                @method("post")
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Name"/>
                    @if ($errors->has('name'))
                        <p class="text-danger">{{ $errors->first('name') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email"/>
                    @if ($errors->has('email'))
                        <p class="text-danger">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password"/>
                    @if ($errors->has('password'))
                        <p class="text-danger">{{ $errors->first('password') }}</p>
                    @endif
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="text" name="password_confirmation" class="form-control" placeholder="Password Confirmation"/>
                </div>
                <div class="row">
                    <div class="col-4 text-right">
                        <input type="submit" class="btn btn-primary" value="Register" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<a href="{{ url('/home') }}">
        <button>Go to Home</button>
    </a>
</div>