@extends('layouts.master')

@section('title')
{{__('vocab.print')}} {{__('vocab.cards')}}
@endsection

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
<a href="{{ route('classes.index') }}">{{__('vocab.classes')}}</a>

@endsection

@section('page')
{{__('vocab.print')}} {{__('vocab.cards')}}
@endsection

@section('content')

@unless (count($students) == 0)
<div class="d-flex flex-column justify-content-center align-items-center my-4">
    <div class="no-print">
        <x-classeinf-card :classe="$classe" />
    </div>
    <div class="btn-group mb-4 gap-2 no-print" role="group">
        <a href="{{ route('classes.printCards', ['classe' => $classe->id]) }}"
            class="btn btn-success text-white font-bold">
            <i class="fas fa-print"></i>
            {{__('vocab.print_to_pdf')}}
        </a>
        <button onclick="window.print()" class="btn btn-outline-success">
            <i class="fa-solid fa-print"></i>
            {{__('vocab.print')}}
        </button>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-center">

        @foreach ($students as $student)
        @if ($student->user && $student->classe)
        <x-printablecard-card :student="$student" />
        @endif
        @endforeach

        <div class="mt-6 p-4 no-print">
            {{ $students->appends(request()->query->all())->links() }}
        </div>
    </div>
</div>
@else
<p>{{__('vocab.no_data')}}</p>
@endunless
@endsection

@section('scripts')
@endsection