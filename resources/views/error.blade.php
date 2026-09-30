@extends('layouts.master')

@section('title')
{{__('vocab.error')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="javascript:history.back()"> {{ __('vocab.go_back') }}</a>
@endsection

@section('page')
{{__('vocab.error')}}
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <img src="{{ asset('assets/images/app_logo.png') }}" alt="App Logo" class="mx-auto h-48 w-48">
        </div>
    </div>
<div class="d-flex justify-content-center align-items-center">
    <x-card :title="__('vocab.error')" >
        @if (isset($message))
        <div class="">
            {{ $message }}
        </div>
        @else
        <div class=" text-danger">{{__('vocab.no_data')}}</div>
        @endif

        <div class="btn-group mt-2">
            <a href="javascript:history.back()" class="btn btn-block btn-outline-warning">
                {{__('vocab.go_back')}}
            </a>
        </div>
    </x-card>
</div>

@endsection

@section('scripts')
@endsection