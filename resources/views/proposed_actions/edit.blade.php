@extends('layouts.master')

@section('title')
{{ __('vocab.edit_proposed_action') }}
@endsection

@section('main-page')
<a href="{{ route('proposed_actions.index') }}">{{ __('vocab.proposed_actions') }} </a>
@endsection

@section('page')
{{ __('vocab.edit_proposed_action') }}
@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">

                    <div class="card-title">{{ __('vocab.edit_proposed_action') }} </div>
                    @error('message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ route('proposed_actions.update',['proposed_action'=>$proposed_action]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group ">
                            <label for="name_ar"> {{__('vocab.name_ar')}} </label>
                            <input type="text" class=" form-control"
                                name="name_ar" value="{{$proposed_action->name_ar}}" id="name_ar" />

                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group ">
                            <label for="name_z"> {{__('vocab.name_z')}} </label>
                            <input type="text" class=" form-control"
                                name="name_z" value="{{$proposed_action->name_z}}" id="name_z" />

                            @error('name_z')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group ">
                            <label for="name_fr"> {{__('vocab.name_fr')}} </label>
                            <input type="text" class=" form-control"
                                name="name_fr" value="{{$proposed_action->name_fr}}" id="name_fr" />

                            @error('name_fr')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="form-group ">
                            <label for="name_en"> {{__('vocab.name_en')}} </label>
                            <input type="text" class=" form-control"
                                name="name_en" value="{{$proposed_action->name_en}}" id="name_en" />

                            @error('name_en')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="card-footer ">

                            <a href="{{ route('proposed_actions.index') }}" class=" btn btn-outline-primary">
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

@endsection

@section('scripts')


@endsection