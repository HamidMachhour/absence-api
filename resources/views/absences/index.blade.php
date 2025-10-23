@extends('layouts.master')

@section('title')
  {{__('vocab.absence_lists')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.index') }}">{{__('vocab.classes')}}</a>
@endsection

@section('page')
  {{__('vocab.absence_lists')}}
@endsection
 
@section('content')
    @unless ($totalCount == 0)
        <div class="card mx-3">
            <div class="card-header">
                <div class=" h4">{{__('vocab.number')}}: {{ $absences->count() }}</div>
                <x-filters-card :params="$params" />
            </div>
            <div class="card-body">
                <div class="lg:grid lg:grid-cols-4">
                        @foreach ($absences as $absence)
                                <x-absence-row :absence="$absence" />
                        @endforeach
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $absences->appends(request()->query->all())->links() }}
            </div>
        </div>
    @else
        <p>{{__('vocab.empty')}}</p>
    @endunless

@endsection

@section('scripts')
@endsection
