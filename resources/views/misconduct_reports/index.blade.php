@extends('layouts.master')

@section('title')
{{ __('vocab.management') }} {{ __('vocab.misconduct_reports') }}
@endsection

@section('main-page')
<a href="{{ route('dashboard') }}">{{ __('vocab.dashboard') }} </a>
@endsection

@section('page')
{{ __('vocab.management') }} {{ __('vocab.misconduct_reports') }}
@endsection

@section('content')

<div class="d-flex justify-content-end m-2 card-body">
    @if(auth()->user()->hasRole('teacher'))
    <a href="{{ route('seances.manage', ['teacherId' => auth()->user()]) }}" class="btn bg-gray">
        <i class="fas fa-plus"></i> {{ __('vocab.new') }}
    </a>
    @endif
</div>

<div class="card-body">

    <x-filters-card :params="$params" />

    <div class="lg:grid lg:grid-cols-3 mt-1">

        @unless (count($misconduct_reports) == 0)

        @foreach ($misconduct_reports as $misconduct_report)
        <x-misconduct-report-card :misconduct_report="$misconduct_report" />
        @endforeach

        <div class="mt-6 p-4">
            {{ $misconduct_reports->appends(request()->query->all())->links() }}
        </div>
        @else
        <p>{{__('vocab.no_data')}}</p>
        @endunless
    </div>
</div>

@endsection

@section('scripts')


@endsection