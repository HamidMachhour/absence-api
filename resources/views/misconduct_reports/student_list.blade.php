@extends('layouts.master')

@section('title')
{{__('vocab.record_report')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('seances.index', ['teacherId' => auth()->user()]) }}">{{__('vocab.seances')}} </a>
@endsection

@section('page')
{{__('vocab.record_report')}}
@endsection

@section('content')

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

<x-seance-ro-card :seance="$seance" />

@unless (count($students) == 0)

<div class="lg:grid lg:grid-cols-4 gap-1 text-ms mb-2">

    @foreach ($students as $student)
    @if ($student->user && $student->classe)
    <x-report-student-card :student="$student" :seance="$seance" />
    @endif
    @endforeach

    <div class="mt-6 p-4">
        {{ $students->appends(request()->query->all())->links() }}
    </div>
</div>
@else
<p>{{__('vocab.no_data')}}</p>
@endunless

@endsection

@section('scripts')
@endsection