@extends('layouts.master')

@section('title')
    اقسامي
@endsection

@section('css')
@endsection

@section('main-page')
    <a href='{{ route('classes.index') }}'> الاقسام</a>
@endsection

@section('page')
    اقسامي
@endsection

@section('content')
    <div class='text-right mr-2'>
        <a href="{{ route('classes.create') }}" class="btn btn-app bg-gray">
            <i class="fas fa-plus"></i>
            قسم جديد
        </a>
    </div>

    <x-filters-card :params="$params" />
    <div class="lg:grid grid-cols-3 gap-2 m-2">

        @unless (count($classes) == 0)
            @foreach ($classes as $classe)
                <x-classe-card :classe="$classe" />
            @endforeach
        @else
            <p>لا شيء</p>
        @endunless
    </div>

    <div class="mt-6 p-4">
        {{ $classes->appends(request()->query->all())->links() }}
    </div>
@endsection

@section('scripts')
@endsection
