@extends('layouts.master')

@section('title')
{{__('vocab.enrollments')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('classes.index') }}">{{__('vocab.classes')}}</a>
@endsection

@section('page')
{{__('vocab.enrollments')}}
@endsection

@section('content')
<form method="GET" class="pb-4 btn-group gap-2 flex justify-center">

    <div class="form-group">
        <select name="sort" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.sort_by')}}
            </option>
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
        <select name="manager" class="form-control" onchange="this.form.submit()">
            <option value=""> {{__('vocab.manager')}}
            </option>
            @foreach ($managers as $manager)
            <option value="{{$manager->id}}" {{ request()->get('manager') === $manager->id ? 'selected' : '' }}>{{ $manager->fullName()  }}</option>
            @endforeach
        </select>
    </div>
      <div class="form-group">
        <a href="{{ route('enrollments.index') }}" class="btn  btn-outline-primary">
            {{__('vocab.clear')}}
        </a>
    </div>
</form>
@unless ($total == 0)

<div class="card mx-3">
    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
        <div class=" h4">{{__('vocab.number')}}: {{ $total }}</div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{__('vocab.date')}}</th>
                    <th>{{__('vocab.enroller')}}</th>
                    <th>{{__('vocab.class')}}</th>
                    <th>
                        <div class="btn-group gap-2">
                            <a href="{{ route('enrollments.acceptAll',$_REQUEST) }}"
                                class="btn bg-success">
                                <i class="fas fa-check-double"></i>
                            </a>
                            <form action="{{ route('enrollments.destroyAll',$_REQUEST) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn bg-danger"
                                    onclick="return confirm('{{ __('vocab.are_you_sure') }}')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($enrollments as $enrollment)
                @if ($enrollment->user && $enrollment->classe)
                <x-enrollment-row :enrollment="$enrollment" />
                @endif
                @endforeach
            </tbody>

        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $enrollments->appends(request()->query->all())->links() }}
    </div>
</div>
@else
<p>{{__('vocab.no_data')}}</p>
@endunless

@endsection

@section('scripts')
@endsection