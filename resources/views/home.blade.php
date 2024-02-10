@extends('layouts.master')

@section('title')
منصة ثانوية ابن هاني التأهيلية بفاس
@endsection

@section('css')

@endsection

@section('preload')
<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ URL::asset('assets/img/logo.png') }}" alt="Manech" height="100"
        width="100">
</div>
@endsection
@section('main-page')
<a href="{{route('home.route')}}">الرئيسية</a>
@endsection

@section('page-title')
الرئيسية
@endsection

@section('page')
استقبال
@endsection

@section('content')
استقبال
@endsection

@section('scripts')

@endsection
