@extends('layouts.master')

@section('title')
{{ __('vocab.edit_subject') }} 
@endsection

@section('main-page')
<a href="{{ route('subjects.index') }}">{{ __('vocab.subjects') }} </a>
@endsection

@section('page')
{{ __('vocab.edit_subject') }} 
@endsection

@section('content')

@if (auth()->user()->hasRole('admin'))

    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">

                        <div class="card-title">{{ __('vocab.edit_subject') }} </div>
                        @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('subjects.update',['subject'=>$subject]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group ">
                                <label for="name" > {{__('vocab.name_ar')}} </label>
                                <input type="text" class=" form-control"
                                    name="name" value="{{$subject->name}}" id="name" />

                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                             <div class="form-group ">
                                <label for="nameZ" > {{__('vocab.name_z')}} </label>
                                <input type="text" class=" form-control"
                                    name="nameZ" value="{{$subject->nameZ}}" id="nameZ" />

                                @error('nameZ')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group ">
                                <label for="nameFr"> {{__('vocab.name_fr')}} </label>
                                <input type="text" class=" form-control"
                                    name="nameFr"  value="{{$subject->nameFr}}"  id="nameFr" />

                                @error('nameFr')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>



                            <div class="form-group ">
                                <label for="nameEn"> {{__('vocab.name_en')}} </label>
                                <input type="text" class=" form-control"
                                    name="nameEn" value="{{$subject->nameEn}}" id="nameEn" />

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
                                    {{__('vocab.update')}}
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