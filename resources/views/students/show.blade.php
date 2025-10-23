@extends('layouts.master')

@section('title')
{{__('vocab.student').' '.__('vocab.info')}}@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('students.index',['classeId'=>$student->classeId]) }}"> التلاميد</a>
@endsection

@section('page')
{{__('vocab.student').' '.__('vocab.info')}}@endsection

@section('content')

<x-classeinf-card :classe="$student->classe" />

        <x-card :title='$student->user->fullName() '>
            <div class="flex flex-col items-center justify-center text-center">
                <div class="flex flex-row items-center justify-center">
                    <div class="d-flex flex-column">
                        <a href="{{ route('userImages.show', ['user' => $student->userId]) }}">
                            <img class="rounded-xl w-48" src="{{ $student->studentImage->uri ?? asset('/assets/images/no-image.png') }}"
                                alt="" />
                        </a>
                    </div>
                    @auth
                        @if (auth()->user()->hasRole('admin') || auth()->user()->id === $student->classe->userId)
                            <x-studentactions-card :student="$student" />
                       {{--  @else
                            <x-studentpublicactions-card :student="$student" /> --}}
                        @endif
                    @endauth
                </div>
                <div class="text-2xl mb-2">
                {{__('vocab.num')}} : {{ $student->num }}
                </div>
            </div>
        </x-card>
@endsection

@section('scripts')
@endsection
