@extends('layouts.master')

@section('title')

{{__('vocab.compose_message')}}

@endsection

@section('css')
@endsection

@section('main-page')
@if($destination_user)
<a href="{{ route('users.show', ['user'=> $destination_user]) }}">{{$destination_user->fullName()}}</a>
@else
<a href="{{ route('messages.inbox') }}">{{__('vocab.inbox')}}</a>
@endif
@endsection

@section('page')
{{__('vocab.compose_message')}}
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">
            {{__('vocab.compose_message')}}
        </h2>
        <form action="{{ route('messages.sent') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="recipient" class="block text-sm font-medium text-gray-700">{{ __('vocab.recipient') }}</label>
                <select name="recipient" id="recipient" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    <option value="">{{ __('vocab.select_recipient') }}</option>
                    @foreach($users as $friend)
                    <option value="{{ $friend->id }}" @if(old('recipient') == $friend->id || ($destination_user && $destination_user->id== $friend->id)) selected @endif>
                        {{ $friend->fullName() }} ({{ $friend->email }})
                    </option>
                    @endforeach
                </select>
                @error('recipient')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">{{ __('vocab.message_subject') }}</label>
                <input type="text" name="subject" id="subject" value="" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                @error('subject')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">{{ __('vocab.message') }}</label>
                <textarea name="message" id="message" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                @error('message')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white btn-app hover:bg-blue-600 px-4 py-2 rounded">
                    {{ __('vocab.send_message') }}  
                </button>
            </div>
        </form>
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