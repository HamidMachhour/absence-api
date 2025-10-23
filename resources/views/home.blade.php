@extends('layouts.master')

@section('title')
{{__('vocab.platform')}} {{__('vocab.absence')}}
@endsection

@section('css')

@endsection

@section('preload')
<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ URL::asset('/assets/images/logo.png') }}" alt="Manech" height="100"
        width="100">
</div>
@endsection
@section('main-page')
<a href="{{route('home')}}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
{{__('vocab.home')}}
@endsection

@section('page')
{{__('vocab.home')}}
@endsection

@section('content')

@include('layouts.home')

@endsection

@section('scripts')

@endsection