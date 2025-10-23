@extends('layouts.master')

@section('title')
    {{__('vocab.add_image')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classeImages.index', ['classe' => $classe->id]) }}">{{__('vocab.classe_images')}} {{ $classe->classeName() }}</a>
@endsection

@section('page-title')
    {{__('vocab.add_image')}}
@endsection

@section('page')
    {{__('vocab.add_image')}}
@endsection
 
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">

                            <h3 class="card-title "> {{__('vocab.add_image')}} </h3>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @csrf
                        <div class='card-body'>

                            <form method="POST" action="{{ route('classeImages.store') }}">
                                @csrf
                                <div class="form-group ">
                                    <input type="text" class="form-control " name="uri"
                                        placeholder="http://..." value="{{ old('uri') }}" />

                                    @error('uri')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <input type="hidden" name="classeId" value="{{ $classe->id }}">

                                <div class="card-footer ">
                                    <a href="javascript:history.back()" class="btn btn-outline-gray">
                                        {{__('vocab.go_back') }}</a>

                                    <button type="submit" class="btn btn-outline-primary">
                                        {{__('vocab.save')}}
                                    </button>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>

        <x-flash-message />
    @endsection

    @section('scripts')
    @endsection
