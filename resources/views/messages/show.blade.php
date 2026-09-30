@extends('layouts.master')

@section('title')
{{__('vocab.view_message')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('messages.inbox') }}">{{__('vocab.inbox')}}</a>
@endsection

@section('page')
{{__('vocab.view_message')}}
@endsection

@section('content')
<div class="card mx-3">
    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
        <div class=" h4">{{ $message->subject }}</div>
    </div>
    <div class="card-body">
        <div class="d-flex flex-column">
            <div class="text-center my-4" >
                {{ $message->sourceUser->fullName()  }}
            </div>
            <div class="text-center my-4">

                {{ $message->destinationUser->fullName()  }}
            </div>
            <div class="text-center my-4">
                {{ $message->message }}
            </div>
            <div class="flex text-gray-700 hover:text-blue-600 justify-center">
                <div>{{ $message->created_at->format('Y-m-d H:i')  }}</div>
            </div>
            <div class="btn-group gap-2 mt-4">
                <form action="{{ route('messages.destroy',['message'=>$message->id]) }}" method="POST"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn bg-danger" title="{{__('vocab.delete')}}" onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
                <a href="{{ route('messages.inbox') }}" title="{{__('vocab.go_back')}}">
                    <div class="btn bg-primary">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                </a>
                <a href="{{ route('messages.reply', ['reply_to' => $message->id]) }}" title="{{__('vocab.reply_message')}}">
                    <div class="btn bg-success">
                        <i class="fa-solid fa-reply"></i>   
                    </div>
                </a>
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