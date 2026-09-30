@extends('layouts.master')

@section('title')
   {{__('vocab.platform')}} {{__('vocab.absence')}}
@endsection

@section('css')
@endsection

@section('preload')
@endsection
@section('main-page')
    <a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
    {{__('vocab.home')}}
@endsection

@section('page')
    {{__('vocab.dashboard')}}
@endsection

@section('content')
    @include('layouts.dashboard.admin')
@endsection

@section('scripts')
<script>
    const VOCAB = {
        'absences': "{{ __('vocab.absences') }}",
        'absence_justified': "{{ __('vocab.absence_justified') }}",
        'absence_not_justified': "{{ __('vocab.absence_not_justified') }}",
        'plages': "{{ __('vocab.plages') }}",
        'compare_sections': "{{ __('vocab.compare_sections') }}",
        'students': "{{ __('vocab.students') }}",
        'draft':"{{ __('vocab.draft') }}",
        'submitted':"{{ __('vocab.submitted') }}",
        'reviewed':"{{ __('vocab.reviewed') }}",
        'archived':"{{ __('vocab.archived') }}",
    };
    
</script>
    @parent
    <script src="{{URL::asset('assets/js/admin.js')}}"></script>
@endsection
