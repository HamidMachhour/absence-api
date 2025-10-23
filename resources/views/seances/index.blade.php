@extends('layouts.master')

@section('title')
{{__('vocab.time_quotas')}} 
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.index') }}">{{__('vocab.classes')}}</a>
@endsection

@section('page')
{{__('vocab.time_quotas')}} 
@endsection

@section('content')

<x-filters-card :params="$params" />

<div class="lg:grid lg:grid-cols-4 mt-1">

    @unless (count($seances) == 0)

        @foreach ($seances as $seance)
            <x-seance-ro-card :seance="$seance" />
        @endforeach

        <div class="mt-6 p-4">
            {{ $seances->appends(request()->query->all())->links() }}
        </div>
    @else
        <p>{{__('vocab.empty')}}</p>
    @endunless
</div>

@endsection

@section('scripts')
@endsection
