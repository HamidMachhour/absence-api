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
{{__('vocab.contact')}}
@endsection

@section('page')
{{__('vocab.contact')}}
@endsection

@section('content')

<div class="container mx-auto px-4 py-8">

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">{{ __('vocab.contact_us') }}</h2>
        <form action="{{ route('messages.contactUs') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">{{ __('vocab.message_subject') }}</label>
                <input type="text" name="subject" id="subject" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">{{ __('vocab.message') }}</label>
                <textarea name="message" id="message" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white btn-app hover:bg-blue-600 px-4 py-2 rounded">
                    {{ __('vocab.send_message') }}
                </button>
            </div>
        </form>
    </div>
    <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">{{ __('vocab.contact_info') }}</h2>
        <div class="space-y-3">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>hamid.machhour@gmail.com</span>
            </div>
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span>+212 652 838 057</span>
            </div>
        </div>
    </div>
</div>
@if(session('success'))
<div class="alert alert-success mt-4">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger mt-4">
    {{ session('error') }}
</div>
@endif
@endsection

@section('scripts')

@endsection