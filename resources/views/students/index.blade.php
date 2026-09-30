@extends('layouts.master')

@section('title')
{{__('vocab.list')}} {{__('vocab.students')}} 
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.index') }}">{{__('vocab.classes')}}</a>
@endsection

@section('page')
{{__('vocab.students')}} 
@endsection

@section('content')

<x-filters-card :params="$params" />

    @unless (count($students) == 0)

        @if (request()->filled('classeId'))
            <x-classeinf-card :classe="$students[0]->classe" />
        @endif

        <div class="lg:grid lg:grid-cols-3 gap-1 mx-1">

            @foreach ($students as $student)
                @if ($student->user && $student->classe)
                    <x-student-card :student="$student" />
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
