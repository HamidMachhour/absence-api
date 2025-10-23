@extends('layouts.master')

@section('title')
{{__('vocab.print')}} {{__('vocab.cards')}}
@endsection

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/css/app.css') }}">

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

    <x-classeinf-card :classe="$classe" />

    <div class="flex justify-end mb-4">
        <a href="{{ route('classes.printCards', ['classe' => $classe->id]) }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-print"></i> {{__('vocab.print')}} {{__('vocab.cards')}}
        </a>
    </div>

    <div class="lg:grid lg:grid-cols-4 gap-1 mx-1">

        @foreach ($students as $student)
        @if ($student->user && $student->classe)
        <x-printablecard-card :student="$student" />
        @endif
        @endforeach

        <div class="mt-6 p-4">
            {{ $students->appends(request()->query->all())->links() }}
        </div>
    </div>
</div>
@else
<p>{{__('vocab.empty')}}</p>
@endunless
@endsection

@section('scripts')
@endsection