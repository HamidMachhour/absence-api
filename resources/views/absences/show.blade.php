@extends('layouts.master')

@section('title')
    {{__('vocab.absence_list')}}@endsection

@section('css')
@endsection

@section('main-page')
    @if (auth()->user()->isStudent())
        <a href="{{ route('absences.ofUser', ['user' => auth()->user()]) }}">{{__('vocab.absence_lists')}} </a>
    @else
        <a href="{{ route('absences.index') }}">{{__('vocab.absence_lists')}} </a>
    @endif
@endsection

@section('page')
    {{__('vocab.absence_list')}}@endsection

@section('content')

    <x-absence-row :absence="$absence" />
    @unless (($absenceStudents = $absence->absenceStudents()->paginate(40))->count() == 0)
        <div class="card mx-3">
            <div class="card-header">
                <div class=" h4">{{__('vocab.number')}}: {{ $absenceStudents->count() }}</div>
            </div>
            <div class="card-body">
                <div class="lg:grid lg:grid-cols-4">
                    @foreach ($absenceStudents as $absenceStudent)
                        <x-absence-student-row :absenceStudent="$absenceStudent" />
                    @endforeach
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $absenceStudents->appends(request()->query->all())->links() }}
            </div>
        </div>
    @else
        <p>{{__('vocab.empty')}}</p>
    @endunless
@endsection

@section('scripts')
@endsection
