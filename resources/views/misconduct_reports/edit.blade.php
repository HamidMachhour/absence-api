@extends('layouts.master')

@section('title')
{{ __('vocab.edit') }} {{ __('vocab.misconduct_reports') }}
@endsection

@section('main-page')
<a href="{{ route('misconduct_reports.index') }}">{{ __('vocab.misconduct_reports') }} </a>
@endsection

@section('page')
{{ __('vocab.edit') }} {{ __('vocab.misconduct_reports') }}
@endsection

@section('content')

@if(session('success'))
<div class="alert alert-success mt-4">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger mt-4">
    {{ session('error') }}
</div>
@endif

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
                    <div class="d-flex flex-row justify-between">
                        <x-student-card :student="$misconduct_report->student" />
                        <x-seance-card :seance="$misconduct_report->seance" />
                    </div>
                    <form method="POST" action="{{ route('misconduct_reports.update',['misconduct_report'=>$misconduct_report]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group mt-1 text-center">
                            <div class="btn-group btn-group-toggle  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }} " data-toggle="buttons">
                                @foreach ($statuses as $status)
                                <label class="btn btn-outline-success {{ $misconduct_report->status == $status ? 'active' : '' }}">
                                    <input type="radio" name="status" id="status_{{ $status }}" value="{{ $status }}" autocomplete="off" {{ $misconduct_report->status == $status ? 'checked' : '' }}> {{ __("vocab.{$status}") }}
                                </label>
                                @endforeach
                            </div>
                             @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                             @enderror
                        </div>
                        <div class="d-flex gap-2 flex-column">
                            <div class="card card-primary card-outline m-1" :title="">

                                <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
                                    <div class="card-title h3">
                                        {{ __('vocab.misconduct_acts')   }}
                                    </div>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /.card-tools -->
                                </div>
                                <div class="card-body p-1">

                                    <div class="d-flex flex-column">
                                        @foreach ($misconductActs as $misconduct_act)
                                            @if (App::getLocale() == 'ar')
                                                <div class="form-check d-flex flex-row-reverse justify-content-between">
                                                    <label class="form-check-label mr-4" for="misconduct_act_{{ $misconduct_act->id }}">{{ $misconduct_act->actName() }}</label>
                                                    <input class="form-check-input" type="checkbox" name="misconduct_act_ids[]"
                                                        value="{{ $misconduct_act->id }}"
                                                        id="misconduct_act_{{ $misconduct_act->id }}"
                                                        {{ $misconduct_act_ids->contains($misconduct_act->id) ? 'checked' : '' }}>
                                                </div>
                                            @else
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="misconduct_act_ids[]"
                                                        value="{{ $misconduct_act->id }}"
                                                        id="misconduct_act_{{ $misconduct_act->id }}"
                                                        {{ $misconduct_act_ids->contains($misconduct_act->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="misconduct_act_{{ $misconduct_act->id }}">
                                                        {{ $misconduct_act->actName() }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                </div>
                                <!-- /.card-body -->


                            </div>
                            <div class="form-group ">
                                <label for="otherActs"> {{__('vocab.other_acts')}} </label>
                                <textarea rows="4" type="text" class=" form-control {{ App::getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                                    name="otherActs" id="otherActs">{{$misconduct_report->otherActs}}</textarea>

                                @error('otherActs')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>



                        <div class="d-flex gap-2 flex-column">
                            <div class="card card-primary card-outline m-1" :title="">

                                <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
                                    <div class="card-title h3">
                                        {{ __('vocab.proposed_actions')   }}
                                    </div>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <!-- /.card-tools -->
                                </div>
                                <div class="card-body p-1">

                                    <div class="d-flex flex-column">
                                        @foreach ($proposedActions as $proposed_action)
                                            @if (App::getLocale() == 'ar')
                                                <div class="form-check d-flex  flex-row-reverse justify-content-between">
                                                    <label class="form-check-label mr-4" for="proposed_action_{{ $proposed_action->id }}">
                                                        {{ $proposed_action->actionName() }}
                                                    </label>
                                                    <input class="form-check-input" type="checkbox" name="proposed_action_ids[]"
                                                        value="{{ $proposed_action->id }}"
                                                        id="proposed_action_{{ $proposed_action->id }}"
                                                        {{ $proposed_action_ids->contains($proposed_action->id) ? 'checked' : '' }}>
                                                </div>
                                            @else
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="proposed_action_ids[]"
                                                        value="{{ $proposed_action->id }}"
                                                        id="proposed_action_{{ $proposed_action->id }}"
                                                        {{ $proposed_action_ids->contains($proposed_action->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="proposed_action_{{ $proposed_action->id }}">
                                                        {{ $proposed_action->actionName() }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                </div>
                                <!-- /.card-body -->


                            </div>

                            <div class="form-group ">
                                <label for="otherPropositions"> {{__('vocab.other_propositions')}} </label>
                                <textarea rows="4" type="text" class=" form-control {{ App::getLocale() == 'ar' ? 'text-right' : 'text-left' }}"
                                    name="otherPropositions" id="otherPropositions">{{$misconduct_report->otherPropositions}}</textarea>

                                @error('otherPropositions')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>


                        <div class="card-footer ">

                            <a href="{{ route('misconduct_reports.index') }}" class=" btn btn-outline-primary">
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