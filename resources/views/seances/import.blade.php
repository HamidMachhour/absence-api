@extends('layouts.master')

@section('title')
     {{__('vocab.import')}} {{__('vocab.seances')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('seances.manage') }}">{{__('vocab.seances')}}</a>
@endsection

@section('page-title')
{{__('vocab.import')}} {{__('vocab.seances')}}
@endsection

@section('page')
{{__('vocab.import')}} {{__('vocab.seances')}}
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
                            <div class="card-title">{{__('vocab.import')}} {{__('vocab.seances')}}</div>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class='card-body'>
                            <form method="POST" action="{{ route('seances.import') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group ">
                                    <input type="file" class="form-control  " name="file" accept=".csv" required/>

                                    @error('file')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="card-footer ">
                                    <a href="{{ route('seances.manage') }}" class="btn btn-outline-gray">
                                        {{__('vocab.go_back')}}
                                    </a>

                                    <button type="submit" class="btn btn-outline-primary">
                                        {{__('vocab.import')}}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-flash-message />
    @endsection

    @section('scripts')
    @endsection