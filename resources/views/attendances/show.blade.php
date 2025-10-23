@extends('layouts.master')

@section('title')
{{__('vocab.absence_list')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('seances.index', ['teacherId' => auth()->user()]) }}">{{__('vocab.time_quotas')}} </a>
@endsection

@section('page')
{{__('vocab.absence_list')}}
@endsection

@section('content')

<x-attendance-card :attendance="$attendance" />

<x-seance-ro-card :seance="$attendance->seance" />


@unless (count($attendance->attendanceStudents) == 0)

<div class="lg:grid lg:grid-cols-4 mt-1">


    @foreach ($attendance->attendanceStudents as $attendance_student)
    <x-attendancestudent-card :attendance_student="$attendance_student" :seance='$attendance->seance' />
    @endforeach
    @else
    <p class="flex justify-center">{{__('vocab.empty')}}</p>

</div>
@endunless
@endsection

@section('scripts')

@endsection