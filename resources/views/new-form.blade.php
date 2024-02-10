@extends('layouts.master')

@section('title')
منصة ثانوية ابن هاني التأهيلية بفاس
@endsection

@section('css')

@endsection

@section('main-page')
<a href="{{route('home.route')}}">الرئيسية</a>
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
                        <h3 class="card-title "> تسجيل الدخول </h3>
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
