@extends('layouts.master')

@section('title')
{{__('vocab.student').' '.__('vocab.card')}}@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/css/app.css') }}">

@endsection

@section('main-page')
<a href="{{ route('students.index', ['classeId' => $student->classeId]) }}"> {{__('vocab.students')}} </a>
@endsection

@section('page')
{{__('vocab.student').' '.__('vocab.card')}}
@endsection

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center my-4">

    <a href="{{ route('students.printCard', ['student' => $student]) }}"
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
        <i class="fas fa-print"></i> {{__('vocab.print')}}
    </a>
    <div class="lg:grid ">
        <x-printablecard-card :student="$student" />
    </div>
</div>
<flash-message />

@endsection

@section('scripts')
@endsection