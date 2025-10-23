@extends('layouts.master')

@section('title')
{{__('vocab.new_account')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
@endsection

@section('page')
{{__('vocab.new_account')}}
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <img src="{{ asset('assets/images/app_logo.png') }}" alt="App Logo" class="mx-auto h-48 w-48">
    </div>
</div>
<div class="d-flex justify-content-center align-items-center">
    <div class="card card-primary">
        <div class="card-header ">
            @if (isset($role) && $role === 'admin')
            <a class="h3"> {{__('vocab.new_account')}}</a>
            <form method="POST" action="{{ route('admins.store') }}">
                @else
                <a class="h3">{{__('vocab.new_account')}}</a>
                <form method="POST" action="{{ route('users.store') }}">
                    @endif
        </div>
        @error('message')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
        @csrf
        <div class='card-body'>


            <div class="form-group ">
                <label for="firstName"> {{__('vocab.last_name')}} </label>
                <input id='firstName' type="text" class="form-control  " name="firstName"
                    value="" id="firstName" />

                @error('firstName')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group ">
                <label for="lastName"> {{__('vocab.first_name')}} </label>
                <input id='lastName' type="text" class="form-control  " name="lastName"
                    value="" id="lastName" />

                @error('lastName')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group ">
                <label for="firstNameZ"> {{__('vocab.last_name_z')}} </label>
                <input id='firstNameZ' type="text" class="form-control  " name="firstNameZ"
                    value="" id="firstNameZ" />

                @error('firstNameZ')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group ">
                <label for="lastNameZ"> {{__('vocab.first_name_z')}} </label>
                <input id='lastNameZ' type="text" class="form-control  " name="lastNameZ"
                    value="" id="lastNameZ" />

                @error('lastNameZ')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group ">
                <label for="firstNameFr"> {{__('vocab.last_name_fr')}} </label>
                <input id='firstNameFr' type="text" class="form-control  " name="firstNameFr"
                    value="" id="firstNameFr" />

                @error('firstNameFr')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group ">
                <label for="lastNameFr"> {{__('vocab.first_name_fr')}} </label>
                <input id='lastNameFr' type="text" class="form-control  " name="lastNameFr"
                    value="" id="lastNameFr" />

                @error('lastNameFr')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group ">
                <label for="firstNameEn"> {{__('vocab.last_name_en')}} </label>
                <input id='firstNameEn' type="text" class="form-control  " name="firstNameEn"
                    value="" id="firstNameEn" />

                @error('firstNameEn')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group ">
                <label for="lastNameEn"> {{__('vocab.first_name_en')}} </label>
                <input id='lastNameEn' type="text" class="form-control  " name="lastNameEn"
                    value="" id="lastNameEn" />

                @error('lastNameEn')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group ">
                <label for="email">{{__('vocab.email')}} </label>
                <input id='email' type="email" class="form-control  " name="email"
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

            <div class="form-group ">
                <label for="password_confirmation">
                    {{__('vocab.confirm_password')}}
                </label>
                <input id='password_confirmation' type="password" class="form-control  "
                    name="password_confirmation" value="{{ old('password_confirmation') }}" />

                @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="card-footer ">
                <button type="submit" class="btn btn-outline-primary">
                    {{__('vocab.register')}}
                </button>
            </div>
            </form>
            <div class="mt-8 ">
                <p>
                    {{__('vocab.already_have_account')}}
                    <a href="{{ route('users.login') }}" class="text-blue-600 hover:text-gray-700">{{__('vocab.login')}}</a>
                </p>
            </div>
        </div>
    </div>
</div>
<x-flash-message />
@endsection

@section('scripts')
@endsection