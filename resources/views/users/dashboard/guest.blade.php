@extends('layouts.master')

@section('title')
   {{__('vocab.platform')}} {{__('vocab.absence')}}
@endsection

@section('css')
@endsection

@section('preload')
@endsection
@section('main-page')
    <a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
    {{__('vocab.home')}}
@endsection

@section('page')
    {{__('vocab.dashboard')}}
@endsection

@section('content')
    @include('layouts.dashboard.guest')
@endsection

@section('scripts')
@endsection
