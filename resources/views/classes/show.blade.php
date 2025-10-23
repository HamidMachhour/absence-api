@extends('layouts.master')

@section('title')
    {{__('vocab.class')}} {{ $classe->classeName() }}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.index') }}"> {{__('vocab.classes')}}</a>
@endsection

@section('page')
    {{__('vocab.class')}} {{ $classe->classeName() }}
@endsection

@section('content')

    
       <x-classeinf-card :classe="$classe" />

        <x-card :title='__("vocab.details")'>
            <div class="text-lg space-y-6 ">
                {{ $classe->description }}
            </div>
        </x-card>
        <x-card :title="__('vocab.images')">
            <div class="lg:grid lg:grid-cols-2 gap-2 mt-2">

                @unless ($classe->classeImages->count() == 0)
                    @foreach ($classe->classeImages as $classe_image)
                        <img class="rounded-xl w-full" src="{{ $classe_image->uri ?? asset('/assets/images/no-image.png') }}"
                            alt="" />
                    @endforeach
                @else
                    <p>لا صور</p>
                @endunless

            </div>
        </x-card>
@endsection

@section('scripts')
@endsection
