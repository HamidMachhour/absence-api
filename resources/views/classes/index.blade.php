@extends('layouts.master')

@section('title')
{{__('vocab.classes')}}
@endsection

@section('css')
@endsection

@section('main-page')

<a href="{{ route('classes.index') }}"> {{__('vocab.classes')}}</a>

@endsection

@section('page')
{{__('vocab.classes')}}
@endsection

@section('content')

@auth
@if (auth()->user()->hasRole('admin'))
<div class="">
    <div class="btn-group gap-2 ">
        <form action="{{ route('classes.destroyAll',$_REQUEST) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-app bg-danger"
                onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                <i class="fas fa-trash"></i>
                {{__('vocab.delete_all')}}
            </button>
        </form>
        <a href="{{ route('export.classes',$_REQUEST) }}" class="btn btn-app bg-orange">
            <i class="fas fa-arrow-up"></i>
            {{__('vocab.export')}}
        </a>
    </div>
</div>
@endif
@endauth
<x-filters-card :params="$params" />
<div class=" h4">{{__('vocab.number')}}: {{ $totalCount }}</div>

<div class="lg:grid grid-cols-3 gap-1 mx-1">

    @unless ($totalCount == 0)

    @foreach ($classes as $classe)
    <x-classe-card :classe="$classe" />
    @endforeach
    @else
    <p>{{__('vocab.no_data')}}</p>
    @endunless
</div>

<div class="mt-6 p-4">
    {{ $classes->appends(request()->query->all())->links() }}
</div>
@endsection

@section('scripts')
@endsection