@extends('layouts.master')

@section('title')
{{ __('vocab.edit_misconduct_act') }}
@endsection

@section('main-page')
<a href="{{ route('misconduct_acts.index') }}">{{ __('vocab.misconduct_acts') }} </a>
@endsection

@section('page')
{{ __('vocab.edit') }}
@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- left column -->
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">

                    <div class="card-title">{{ __('vocab.edit') }} </div>
                    @error('message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ route('misconduct_acts.update',['misconduct_act'=>$misconduct_act]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group ">
                            <label for="name_ar"> {{__('vocab.name_ar')}} </label>
                            <input type="text" class=" form-control"
                                name="name_ar" value="{{$misconduct_act->name_ar}}" id="name_ar" />

                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group ">
                            <label for="name_z"> {{__('vocab.name_z')}} </label>
                            <input type="text" class=" form-control"
                                name="name_z" value="{{$misconduct_act->name_z}}" id="name_z" />

                            @error('name_z')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group ">
                            <label for="name_fr"> {{__('vocab.name_fr')}} </label>
                            <input type="text" class=" form-control"
                                name="name_fr" value="{{$misconduct_act->name_fr}}" id="name_fr" />

                            @error('name_fr')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="form-group ">
                            <label for="name_en"> {{__('vocab.name_en')}} </label>
                            <input type="text" class=" form-control"
                                name="name_en" value="{{$misconduct_act->name_en}}" id="name_en" />

                            @error('name_en')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="card-footer ">

                            <a href="{{ route('misconduct_acts.index') }}" class=" btn btn-outline-primary">
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