@extends('layouts.master')

@section('title')
    {{ __('vocab.import').' '.__('vocab.users') }}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('users.index') }}">{{__('vocab.users') }}</a>
@endsection

@section('page-title')
{{ __('vocab.import').' '.__('vocab.users') }}
@endsection

@section('page')
{{ __('vocab.import').' '.__('vocab.users') }}
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <div class="card-title">{{ __('vocab.import').' '.__('vocab.users') }}</div>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class='card-body'>
                            <form method="POST" action="{{ route('users.import') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group ">
                                    <label for="file">{{ __('vocab.file') }}</label>
                                    <input type="file" class="form-control  " name="file" accept=".csv" required />


                                    @error('file')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="card-footer ">
                                    <a href="{{route('users.index')}}" class="btn btn-outline-gray">
                                        {{__('vocab.go_back')}}
                                    </a>

                                    <button type="submit" class="btn btn-outline-primary">
                                       {{ __('vocab.import').' '.__('vocab.users') }}
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-flash-message />
    </section>
@endsection

@section('scripts')
@endsection