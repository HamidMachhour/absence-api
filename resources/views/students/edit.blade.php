@extends('layouts.master')

@section('title')
{{__('vocab.edit')}} {{__('vocab.info')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('students.manage',['classe'=>$student->classeId]) }}">{{__('vocab.class_management')}}</a>
@endsection

@section('page-title')
{{__('vocab.edit')}} {{__('vocab.info')}}
@endsection

@section('page')
{{__('vocab.edit')}} {{__('vocab.info')}}
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
                            <h3 class="card-title">{{__('vocab.edit')}} {{__('vocab.info')}}
                            </h3><br>
                            <p class="mb-4">{{ $student->user->fullName() }}</p>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @csrf
                        <div class='card-body'>
                            <form method="POST" action="{{ route('students.update', ['student' => $student->id]) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="form-group ">
                                    <label for="num" >{{__('vocab.num')}}</label>
                                    <input type="text" class="form-control  "
                                        name="num" id="num" placeholder="Example: 1,2,3,...,20" value="{{ $student->num }}" />

                                    @error('num')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="classeId" >{{__('vocab.class')}}</label>
                                    <select id="classeId" name="classeId"
                                        class="form-control  ">
                                        @foreach ($classes as $classe)
                                            <option value="{{ $classe->id }}" {{ $classe->id === $student->classeId ? 'selected' : '' }}>
                                                {{ $classe->classeName() }}</option>
                                        @endforeach
                                    </select>
                                    @error('classeId')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="card-footer ">
                                    <button type="submit" class="btn  btn-outline-success">
                                        <i class="fa-solid fa-save"></i>
                                        {{__('vocab.save')}}
                                    </button>
                                    <a href="javascript:history.back()" class=" btn btn-outline-primary">
                                        <i class="fa-solid fa-circle-arrow-left"></i>
                                        {{__('vocab.go_back')}}
                                    </a>
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
