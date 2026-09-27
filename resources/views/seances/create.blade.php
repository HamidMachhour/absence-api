@extends('layouts.master')

@section('title')
    {{__('vocab.new_seance')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('seances.manage') }}">{{__('vocab.time_quotas')}} 
    </a>
@endsection

@section('page-title')
    {{__('vocab.new_seance')}}
@endsection

@section('page')
    {{__('vocab.new_seance')}}
@endsection
@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
                            <h3 class="card-title">  {{__('vocab.new_seance')}}
                            </h3>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @csrf
                        <div class='card-body'>
                            <form method="POST" action="{{ route('seances.store') }}">
                                @csrf

                                <div class="form-group ">
                                    <label for="teacherId">{{__('vocab.teacher')}}</label>
                                    <select id="teacherId" name="teacherId" class="form-control  ">
                                        <option value=""></option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">
                                                {{ $teacher->fullName()  }}</option>
                                        @endforeach
                                    </select>
                                    @error('teacherId')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label for="classeId">{{__('vocab.class')}}</label>
                                    <select id="classeId" name="classeId" class="form-control  ">
                                        <option value=""></option>
                                        @foreach ($classes as $classe)
                                            <option value="{{ $classe->id }}">
                                                {{ $classe->classeName() }}</option>
                                        @endforeach
                                    </select>
                                    @error('classeId')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label for="subjectId">{{__('vocab.subject')}}</label>
                                    <select id="subjectId" name="subjectId" class="form-control  ">
                                        <option value=""></option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->subjectName() }}</option>
                                        @endforeach
                                    </select>
                                    @error('subjectId')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label for="dayId">{{__('vocab.day')}}</label>
                                    <select id="dayId" name="dayId" class="form-control  ">
                                        <option value=""></option>
                                        @foreach ($jours as $day)
                                            <option value="{{ $day->id }}">
                                                {{ $day->dayName() }}</option>
                                        @endforeach
                                    </select>
                                    @error('dayId')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="de">{{__('vocab.from')}}</label>
                                    <select id="de" name="de" class="form-control  ">
                                        <option value=""></option>
                                        @foreach ($hours as $hour)
                                            <option value="{{ $hour }}">
                                                {{ $hour }}</option>
                                        @endforeach
                                    </select>
                                    @error('de')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="a">{{__('vocab.to')}}</label>
                                    <select id="a" name="a" class="form-control  ">
                                        <option value=""></option>
                                        @foreach ($hours as $hour)
                                            <option value="{{ $hour }}">
                                                {{ $hour }}</option>
                                        @endforeach
                                    </select>
                                    @error('a')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="salle">
                                        {{__('vocab.room')}}
                                    </label>
                                    <input type="number" class="form-control  " name="salle"
                                        value="{{ old('salle') }}" />
                                    @error('salle')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="card-footer ">
                                    <button type="submit" class="btn  btn-outline-success">
                                        <i class="fa-solid fa-save"></i>
                                        {{__('vocab.add')}}
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
