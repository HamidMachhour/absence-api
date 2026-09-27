@extends('layouts.master')

@section('title')
{{__('vocab.time_quotas')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('classes.index') }}">{{__('vocab.classes')}}</a>
@endsection

@section('page')
{{__('vocab.time_quotas')}}
@endsection

@section('content')
<form method="GET" class="pb-4 btn-group gap-2 flex justify-center {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">

    <div class="form-group">
        <select name="sort" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.sort_by')}}
            </option>
            <option value="from_asc" {{ request()->get('sort') === 'from_asc' ? 'selected' : '' }}>{{__('vocab.from_asc')}}</option>
            <option value="to_asc" {{ request()->get('sort') === 'to_asc' ? 'selected' : '' }}>{{__('vocab.to_asc')}}</option>
            <option value="day_asc" {{ request()->get('sort') === 'day_asc' ? 'selected' : '' }}>{{__('vocab.day_asc')}}</option>
            <option value="day_desc" {{ request()->get('sort') === 'day_desc' ? 'selected' : '' }}>{{__('vocab.day_desc')}}</option>

            <option value="newest" {{ request()->get('sort') === 'newest' ? 'selected' : '' }}>{{__('vocab.newest')}}</option>
            <option value="oldest" {{ request()->get('sort') === 'oldest' ? 'selected' : '' }}>{{__('vocab.oldest')}}</option>
        </select>
    </div>
    <div class="form-group">
        <select name="classeId" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.class')}}
            </option>
            @foreach ($classes as $class)
            <option value="{{$class->id}}" {{ request()->get('classeId') === $class->id ? 'selected' : '' }}>{{ $class->classeName()}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <select name="subjectId" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.subject')}}
            </option>
            @foreach ($subjects as $subject)
            <option value="{{$subject->id}}" {{ request()->get('subjectId') === $subject->id ? 'selected' : '' }}>{{ $subject->subjectName()}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <select name="teacherId" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.teacher')}}
            </option>
            @foreach ($teachers as $teacher)
            <option value="{{$teacher->id}}" {{ request()->get('teacherId') === $teacher->id ? 'selected' : '' }}>{{ $teacher->fullName()}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <select name="dayId" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.day')}}
            </option>
            @foreach ($days as $day)
            <option value="{{$day->id}}" {{ request()->get('dayId') == $day->id ? 'selected' : '' }}>{{ $day->dayName()}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <select name="de" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.from')}}
            </option>
            @foreach ($plages as $plage)
            <option value="{{$plage}}" {{ request()->get('de') === $plage ? 'selected' : '' }}>{{ $plage}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <select name="a" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.to')}}
            </option>
            @foreach ($plages as $plage)
            <option value="{{$plage}}" {{ request()->get('a') === $plage ? 'selected' : '' }}>{{ $plage}}</option>
            @endforeach
        </select>
    </div>
<!--
    <div class="form-group">
        <button type="submit" class="btn btn-outline-primary"> {{__('vocab.apply_filters')}}
        </button>
    </div>
-->
    <div class="form-group">
        <a href="{{ route('seances.manage') }}" class="btn  btn-outline-primary">
            {{__('vocab.clear')}}
        </a>
    </div>
</form>
@auth
@if (auth()->user()->hasRole('admin'))
<div class="">
    <div class="btn-group gap-2 ">
        <form action="{{ route('seances.destroyAll',$_REQUEST) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-app bg-danger"
                onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                <i class="fas fa-trash"></i>
                {{__('vocab.delete_all')}}
            </button>
        </form>
        <a href="{{ route('seances.importForm') }}" class="btn btn-app bg-warning">
            <i class="fas fa-arrow-down"></i>
            {{__('vocab.import')}}
        </a>
        <a href="{{ route('seances.export',$_REQUEST) }}" class="btn btn-app bg-orange">
            <i class="fas fa-arrow-up"></i>
            {{__('vocab.export')}}
        </a>

        <a href="{{ route('seances.create') }}" class="btn btn-app bg-gray">
            <i class="fas fa-plus"></i>
            {{__('vocab.new_seance')}}
        </a>
    </div>
</div>
@endif
@endauth

<x-filters-card :params="$params" />

<div class="lg:grid lg:grid-cols-4 mt-1">

    @unless (count($seances) == 0)
    @foreach ($seances as $seance)
    <x-seance-card :seance="$seance" />
    @endforeach

    <div class="mt-6 p-4">
        {{ $seances->appends(request()->query->all())->links() }}
    </div>
    @else
    <p>{{__('vocab.no_data')}}</p>
    @endunless
</div>

@if (session('added_count') && session('failed_count'))
    <div class="alert alert-info">
        <p>Import successful!</p>
        <p>Added records: {{ session('added_count') }}</p>
        <p>Failed records: {{ session('failed_count') }}</p>
        <a href="{{ Storage::url(session('summary_path')) }}" class="btn btn-primary mt-2">Download Summary</a>
    </div>
@endif

@endsection

@section('scripts')
@endsection