@extends('layouts.master')

@section('title')
    منصة ثانوية ابن هاني التأهيلية بفاس
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('home.route') }}">الرئيسية</a>
@endsection

@section('page-title')
@endsection

@section('page')
انشاء حساب جديد
@endsection
 
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            @if (isset($role) && $role === 'admin')
                                <h3 class="card-title "> انشاء حساب لمدير الموقع </h3>
                                <form method="POST" action="{{ route('admins.store') }}">
                                @else
                                    <h3 class="card-title ">انشاء حساب جديد</h3>
                                    <form method="POST" action="{{ route('users.store') }}">
                            @endif
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @csrf
                        <div class='card-body'>
                            <div class="form-group text-right">
                                <label for="firstName" > الاسم العائلي </label>
                                <input id='firstName' type="text" class="form-control text-right"
                                    name="firstName" value="{{ old('firstName') }}" id="firstName" />

                                @error('firstName')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group text-right">
                                <label for="lastName" > الاسم الشخصي </label>
                                <input id='lastName' type="text" class="form-control text-right"
                                    name="lastName" value="{{ old('lastName') }}" id="lastName" />

                                @error('lastName')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group text-right">
                                <label for="email" >البريد الالكتروني </label>
                                <input id='email' type="email" class="form-control text-right"
                                    name="email" value="{{ old('email') }}" />

                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group text-right">
                                <label for="password" >
                                    القن السري
                                </label>
                                <input id='password' type="password" class="form-control text-right"
                                    name="password" value="{{ old('password') }}" />

                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group text-right">
                                <label for="password_confirmation" >
                                    تاكيد القن السري
                                </label>
                                <input id='password_confirmation' type="password"
                                    class="form-control text-right" name="password_confirmation"
                                    value="{{ old('password_confirmation') }}" />

                                @error('password_confirmation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="card-footer text-right">
                                <button type="submit"
                                    class="btn btn-outline-primary">
                                    تسجيل
                                </button>
                            </div>
                            </form>
                            <div class="mt-8 text-right">
                                <p>
                                    تمتلك حساب?
                                    <a href="{{ route('users.login') }}" class="text-orange-700 hover:text-green-700">اضغط
                                        هنا</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/.col (left) -->
            <!-- right column -->
            <div class="col-md-6">

            </div>
            <!--/.col (right) -->
        </div>

        </div>

        <x-flash-message />
    @endsection

    @section('scripts')
    @endsection
