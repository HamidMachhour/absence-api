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
    تسجيل الدخول
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"> تسجيل الدخول </h3>
                        </div>
                        <form method="POST" action="{{ route('users.authenticate') }}">
                            @csrf
                            <div class='card-body'>
                                <div class="form-group text-right">

                                    <label for="email">Email البريد الالكتروني</label>
                                    <input id='email' type="email" class="form-control text-right" name="email"
                                        value="{{ old('email') }}" />
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group text-right">
                                    <label for="password">
                                        القن السري
                                    </label>
                                    <input id='password' type="password" class="form-control text-right" name="password"
                                        value="{{ old('password') }}" />
                                    @error('password')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn  btn-outline-primary">
                                        دخول
                                    </button>
                                </div>
                        </form>
                        <div class="mt-8 text-right">
                            <p>
                                ليس لديك، حساب?
                                <a href="{{ route('users.register') }}"
                                    class="text-orange-700 hover:text-green-700">انشاء حساب جديد</a>
                            </p>
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
