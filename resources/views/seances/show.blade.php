@extends('layouts.master')

@section('title')
{{__('vocab.seance')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('seances.index', ['teacherId' => auth()->user()]) }}">{{__('vocab.time_quotas')}} </a>
@endsection

@section('page')
{{__('vocab.seance')}}
@endsection

@section('content')

<x-seance-card :seance="$seance" />

@endsection

@section('scripts')
@endsection
