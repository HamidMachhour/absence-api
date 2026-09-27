@extends('layouts.master')

@section('title')
    {{__('vocab.record_absence')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('seances.index', ['teacherId' => auth()->user()]) }}">{{__('vocab.time_quotas')}} </a>
@endsection

@section('page')
    {{__('vocab.record_absence')}}
@endsection
 
@section('content')

    <div class="info-box mx-2">
        <a href="{{ route('attendances.show', ['attendance' => $attendance]) }}" class="info-box-icon bg-info">
            <i class="fas fa-person-running"></i>
        </a>

        <div class="info-box-content">
            <span class="info-box-text">{{__('vocab.absence_list')}}
            </span>
            <span class="info-box-number">{{ count($attendance->attendanceStudents) }}</span>
        </div>
    </div>

    <x-seance-ro-card :seance="$attendance->seance"/>

    @unless (count($students) == 0)
        <form method="GET" action="{{ route('attendances.pushAllStudents',['attendance'=>$attendance]) }}">
            <div class="btn-group mx-2 ">
                <button type="submit" class="btn btn-app bg-warning" onclick="return confirm('{{ __('vocab.confirm')}}')">
                    <i class="fas fa-hand"></i>
                    {{__('vocab.collective_absence')}}
                </button>
            </div>
        </form>

        <div class="lg:grid lg:grid-cols-4 gap-1 text-ms mb-2">

            @foreach ($students as $student)
                @if ($student->user && $student->classe)
                    <x-studentattendance-card :student="$student" :attendance="$attendance" />
                @endif
            @endforeach

            <div class="mt-6 p-4">
                {{ $students->appends(request()->query->all())->links() }}
            </div>
        </div>
    @else
        <p>{{__('vocab.no_data')}}</p>
    @endunless
@endsection

@section('scripts')
@endsection
