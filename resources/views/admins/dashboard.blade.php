@extends('layouts.master')

@section('title')
منصة ثانوية ابن هاني التأهيلية بفاس
@endsection

@section('css')

@endsection

@section('preload')
@endsection
@section('main-page')
<a href="{{route('home.route')}}">الرئيسية</a>
@endsection

@section('page-title')
الرئيسية
@endsection

@section('page')
لوحة التحكم
@endsection

@section('content')
@include('layouts.content')
@endsection

@section('scripts')

@endsection

