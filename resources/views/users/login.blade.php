@extends('layouts.master')

@section('title')
{{__('vocab.login')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
@endsection

@section('page')
{{__('vocab.login')}}
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <img src="{{ asset('assets/images/app_logo.png') }}" alt="App Logo" class="mx-auto h-48 w-48">
    </div>
</div>
<div class="d-flex justify-content-center align-items-center">
    <div class="card card-primary ">
        <div class="card-header ">
            <a class="h3"> {{__('vocab.login')}} </a>
        </div>
        @error('message')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
        <form method="POST" action="{{ route('users.authenticate') }}">
            @csrf
            <div class='card-body'>
                <div class="form-group ">
                    <label for="email">{{__('vocab.email')}}</label>
                    <input id='email' type="email" class="form-control " name="email"
                        value="{{ old('email') }}" />
                    @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group ">
                    <label for="password">
                        {{__('vocab.password')}}
                    </label>
                    <input id='password' type="password" class="form-control  " name="password"
                        value="{{ old('password') }}" />
                    @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                </div>

                <div class="card-footer ">
                    <button type="submit" class="btn  btn-outline-primary">
                        {{__('vocab.login')}}
                    </button>
                </div>
        </form>
        <div class="mt-8 ">
            <p>
                {{__('vocab.not_have_account')}}
                <a href="{{ route('users.register') }}" class="text-blue-600 hover:text-gray-700">{{__('vocab.new_account')}}</a>
            </p>
        </div>
        <div class="mt-8 ">
            <p>
                {{__('vocab.forgot_password')}}
            </p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection