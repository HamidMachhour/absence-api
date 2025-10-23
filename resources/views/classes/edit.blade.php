@extends('layouts.master')

@section('title')
    {{__('vocab.edit')}} {{__('vocab.class')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.manage') }}">{{__('vocab.my_classes')}}</a>
@endsection

@section('page-title')
{{__('vocab.edit')}} {{__('vocab.class')}}
@endsection

@section('page')
    {{__('vocab.edit')}} {{__('vocab.class')}}
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">

                            <div class="card-title">{{ $classe->classeName() }} {{__('vocab.edit')}} {{__('vocab.class')}}</div>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @csrf
                        <div class='card-body'>

                            <form method="POST" action="{{ route('classes.update', ['classe' => $classe->id]) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="form-group ">
                                    <label for="name">{{__('vocab.classe_name')}}</label>
                                    <input type="text" class="form-control "
                                        name="name" placeholder="{{__('vocab.class_placeholder')}}"
                                        value="{{ $classe->name }}" />

                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="nameZ">{{__('vocab.classe_name_z')}}</label>
                                    <input type="text" class="form-control " name="nameZ"
                                        placeholder="{{__('vocab.class_placeholder')}}" value="{{ $classe->nameZ }}" />

                                    @error('nameZ')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="nameFr">{{__('vocab.classe_name_fr')}}</label>
                                    <input type="text" class="form-control " name="nameFr"
                                        placeholder="{{__('vocab.class_placeholder')}}" value="{{ $classe->nameFr }}" />

                                    @error('nameFr')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="nameEn">{{__('vocab.classe_name_en')}}</label>
                                    <input type="text" class="form-control " name="nameEn"
                                        placeholder="{{__('vocab.class_placeholder')}}" value="{{ $classe->nameEn }}" />

                                    @error('nameEn')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="tags" >
                                        {{__('vocab.tags')}}
                                    </label>
                                    <input type="text" class="form-control "
                                        name="tags"  value="{{ $classe->tags }}" />

                                    @error('tags')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div class="form-group ">
                                    <label for="description" >
                                        {{__('vocab.details')}} {{__('vocab.class')}}
                                    </label>
                                    <textarea class="form-control " name="description" rows="4"
                                        placeholder="{{__('vocab.details')}} ....">{{ $classe->description }}</textarea>

                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div class="card-footer ">
                                    <a href="{{ route('students.manage', ['classe' => $classe->id]) }}" class="btn btn-outline-gray">
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

        <x-flash-message />
    @endsection

    @section('scripts')
    @endsection
