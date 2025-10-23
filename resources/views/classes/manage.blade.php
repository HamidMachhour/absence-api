@extends('layouts.master')

@section('title')
    {{__('vocab.my_classes')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.index') }}"> {{__('vocab.classes')}}</a>
@endsection

@section('page')
    {{__('vocab.my_classes')}}
@endsection

@section('content')
    <div class=' mr-2'>
        <a href="{{ route('classes.create') }}" class="btn btn-app bg-gray">
            <i class="fas fa-plus"></i>
            {{__('vocab.new')}}
        </a>
        <a href="{{ route('importForm.classes') }}" class="btn btn-app bg-info">
            <i class="fas fa-arrow-down"></i>
            {{__('vocab.import')}}
        </a>
        <a href="{{ route('export.classes') }}" class="btn btn-app bg-success">
            <i class="fas fa-arrow-up"></i>
            {{__('vocab.export')}}
        </a>
    </div>

    <x-filters-card :params="$params" />
    <div class=" h4">{{__('vocab.number')}}: {{ $totalCount }}</div>
    <div class="lg:grid grid-cols-3 gap-2 m-2">

        @unless ($totalCount == 0)
            @foreach ($classes as $classe)
                <x-classe-card :classe="$classe" />
            @endforeach
        @else
            <p>{{__('vocab.empty')}}</p>
        @endunless
    </div>

    <div class="mt-6 p-4">
        {{ $classes->appends(request()->query->all())->links() }}
    </div>
@endsection

@section('scripts')
@endsection
