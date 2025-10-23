@extends('layouts.master')

@section('title')
{{__('vocab.inbox')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page')
{{__('vocab.inbox')}}
@endsection

@section('content')
@unless ($totalCount == 0)
<div class="card mx-3">
    <div class="card-header">
        <div class=" h4">{{__('vocab.number')}}: {{ $messages->count() }}</div>
        <x-filters-card :params="$params" />
    </div>
    <div class="card-body">
        <div class="lg:grid lg:grid-cols-4">
            @foreach ($messages as $message)
            <x-message-row :message="$message" />
            @endforeach
        </div>
    </div>
    <div class="card-footer clearfix">
        {{ $messages->appends(request()->query->all())->links() }}
    </div>
</div>
@else
<p>{{__('vocab.empty')}}</p>
@endunless
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