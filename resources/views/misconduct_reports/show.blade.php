@extends('layouts.master')

@section('title')
{{ __('vocab.view') }} {{ __('vocab.misconduct_report') }}
@endsection

@section('main-page')
<a href="{{ route('misconduct_reports.index') }}">{{ __('vocab.misconduct_reports') }} </a>
@endsection

@section('page')
{{ __('vocab.view') }} {{ __('vocab.misconduct_report') }}
@endsection

@section('css')
<style>
    .report-container {
        font-family: 'Arial', sans-serif;
        font-size: 1rem;
        line-height: 2;
        border: 1px solid #dee2e6;
        padding: 6rem;
        background-color: #fff;
    }

    .report-body {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .report-section {
        margin-bottom: 1rem;
    }

    .report-list {
        list-style-type: '✓ ';
        /* Use logical properties for padding. 'start' is right in RTL, left in LTR. */
        padding-inline-start: 2rem;
        padding-right: 2rem;

    }

    .report-title {
        font-weight: bold;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .dynamic-data {
        font-weight: bold;
        display: inline-block;
        min-width: 150px;
        border-bottom: 1px dotted #000;
        padding: 0 5px;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        .report-container,
        .report-container * {
            visibility: visible;
        }

        .report-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none;
        }

        .no-print {
            display: none;
        }
    }
</style>
 
{{-- Add specific RTL rules for the list when the locale is Arabic --}}
@if (App::getLocale() == 'ar')
<style>
    .report-list {
        direction: rtl;
    }
</style>
@endif
@endsection

@section('content')

<div class="container-fluid">
    <div class="report-container my-2 d-flex flex-column text-start">
        <div class="report-header d-flex flex-column ">
            <div class="d-flex {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}"><span class="dynamic-data"> {{ \App\Models\SchoolConfiguration::getSettings()?->schoolName() }} </span></div>

            <div class="d-flex {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">{{__('vocab.teacher')}} <span class="dynamic-data">{{ $misconduct_report->seance->teacher->fullName() }}</span></div>
            <div class="d-flex {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">{{__('vocab.subject')}} <span class="dynamic-data">{{ $misconduct_report->seance->subject->subjectName() }}</span></div>
            <div class="d-flex {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}"> {{__('vocab.date')}} <span class="dynamic-data">{{ $misconduct_report->reportDate }}</span></div>
        </div>

        <div class="report-body d-flex flex-column ">
            <div class="report-title text-center">{{ __('vocab.report_subject_title') }}</div>

            <div class="report-section d-flex flex-column ">
                <div class="d-flex {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">{{ __('vocab.regarding_the_student') }}<span class="dynamic-data">{{ $misconduct_report->student->user->fullName() }}</span></div>
                <div class="d-flex {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">{{__('vocab.class')}} <span class="dynamic-data">{{ $misconduct_report->student->classe->classeName() }}</span></div>
                <div class="d-flex {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">{{__('vocab.num')}}<span class="dynamic-data">{{ $misconduct_report->student->num }}</span></div>
            </div>

            <div class="report-section d-flex flex-column ">
                <p>{{ __('vocab.report_body_intro_acts') }}</p>
                <ul class="report-list">
                    @foreach($misconductActs as $act)
                    <li>{{ $act->actName() }}</li>
                    @endforeach
                </ul>
                @if($misconduct_report->otherActs)
                <p> {{__('vocab.other_acts') }} <span class="dynamic-data">{{ $misconduct_report->otherActs }}</span></p>
                @endif
            </div>

            <div class="report-section d-flex flex-column ">
                <p>{{ __('vocab.report_body_proposed_action_intro') }}</p>
                <ul class="report-list">
                    @foreach($proposedActions as $action)
                    <li>{{ $action->actionName() }}</li>
                    @endforeach
                </ul>
                @if($misconduct_report->otherPropositions)
                <p>{{__('vocab.other_propositions') }} <span class="dynamic-data">{{ $misconduct_report->otherPropositions }}</span></p>
                @endif
            </div>
        </div>

        <div class="report-footer d-flex flex-column align-items-{{ App::getLocale() == 'ar' ? 'end' : 'start' }}">
            <p>{{ __('vocab.report_footer_conclusion') }}</p>
            <p>{{ __('vocab.report_footer_greeting') }}</p>
            <div class="report-signature d-flex {{ App::getLocale() == 'ar' ? '' : 'flex-row-reverse' }}">
                <p>{{ __('vocab.report_signature') }}</p>
            </div>
        </div>
    </div>

    <div class="card-footer no-print">
        <a href="{{ route('misconduct_reports.index') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-circle-arrow-left"></i>
            {{__('vocab.go_back')}}
        </a>
        <button onclick="window.print()" class="btn btn-outline-success">
            <i class="fa-solid fa-print"></i>
            {{__('vocab.print')}}
        </button>
    </div>
</div>

@endsection