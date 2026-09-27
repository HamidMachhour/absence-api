@extends('layouts.master')

@section('title')
{{ __('vocab.new_subject') }} 
@endsection

@section('main-page')
<a href="{{ route('subjects.index') }}">{{ __('vocab.subjects') }} </a>
@endsection

@section('page')
{{ __('vocab.new_subject') }} 
@endsection

@section('content')

@if (auth()->user()->hasRole('admin'))

    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">

                        <div class="card-title">{{ __('vocab.new_subject') }}</div>
                        @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('subjects.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group ">
                                <label for="name" > {{__('vocab.name_ar')}} </label>
                                <input type="text" class=" form-control"
                                    name="name" id="name" />

                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                             <div class="form-group ">
                                <label for="nameZ" > {{__('vocab.name_z')}} </label>
                                <input type="text" class=" form-control"
                                    name="nameZ"  id="nameZ" />

                                @error('nameZ')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group ">
                                <label for="nameFr"> {{__('vocab.name_fr')}} </label>
                                <input type="text" class=" form-control"
                                    name="nameFr" value="" id="nameFr" />

                                @error('nameFr')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>



                            <div class="form-group ">
                                <label for="nameEn"> {{__('vocab.name_en')}} </label>
                                <input type="text" class=" form-control"
                                    name="nameEn" value="" id="nameEn" />

                                @error('nameEn')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="card-footer ">

                                 <a href="{{ route('subjects.index') }}" class=" btn btn-outline-primary">
                                    <i class="fa-solid fa-circle-arrow-left"></i>
                                    {{__('vocab.go_back')}}
                                </a>

                                <button type="submit" class="btn  btn-outline-success">
                                    <i class="fa-solid fa-save"></i>
                                    {{__('vocab.add')}}
                                </button>
                                
                               
                                
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@else


<p>{{ __('vocab.not_authorized') }}</p>

@endif

@endsection

@section('scripts')


@endsection