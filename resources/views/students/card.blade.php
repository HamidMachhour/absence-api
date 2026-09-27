@extends('layouts.master')

@section('title')
{{__('vocab.student').' '.__('vocab.card')}}@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/css/app.css') }}">
<style>
    @media print {
        @page {
            size: landscape;
            /*size: portrait;*/
            /* Suggests landscape orientation */
        }

        .no-print {
            display: none;
        }

        .printable-card-class {
            /* Prevents a page break *inside* the element */
            page-break-inside: avoid;
        }
    }
</style>
@endsection

@section('main-page')
<a href="{{ route('students.index', ['classeId' => $student->classeId]) }}"> {{__('vocab.students')}} </a>
@endsection

@section('page')
{{__('vocab.student').' '.__('vocab.card')}}
@endsection

@section('content')
<div class="d-flex flex-column justify-content-center align-items-center my-4">
    <div class="btn-group mb-4 gap-2 no-print" role="group">
        <a href="{{ route('students.printCard', ['student' => $student]) }}"
            class="btn btn-success text-white font-bold">
            <i class="fas fa-print"></i>
            {{__('vocab.print_to_pdf')}}
        </a>
        <button onclick="window.print()" class="btn btn-outline-success">
            <i class="fa-solid fa-print"></i>
            {{__('vocab.print')}}
        </button>
    </div>

    <div class="lg:grid ">
        <x-printablecard-card :student="$student" />
    </div>
</div>
<flash-message />

@endsection

@section('scripts')
@endsection