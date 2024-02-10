{{-- <x-layout>
    <x-card class="flex justify-center text-3xl mb-2">
        حسابات المستعملين
    </x-card>
    <x-filters-card :params="$params" />

    <div class="lg:grid lg:grid-cols-4 gap-2 mt-2">

        @unless (count($users) == 0)

            @foreach ($users as $user)
                <x-user-card :user="$user" />
            @endforeach
        @else
            <p>لا شيء</p>
        @endunless

    </div>
    <div class="mt-6 p-4">
        {{ $users->appends(request()->query->all())->links() }}
    </div>
</x-layout> --}}

@extends('layouts.master')

@section('title')
    منصة ثانوية ابن هاني التأهيلية بفاس
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('users.index') }}">الاعضاء</a>
@endsection

@section('page-title')
    الاعضاء
@endsection

@section('page')
@endsection

@section('content')
    <x-card class="flex justify-center text-3xl mb-2">
        حسابات المستعملين
    </x-card>
    <x-filters-card :params="$params" />

    <div class="lg:grid lg:grid-cols-4 gap-2 mt-2">

        @unless (count($users) == 0)
            @foreach ($users as $user)
                <x-user-card :user="$user" />
            @endforeach
        @else
            <p>لا شيء</p>
        @endunless

    </div>
    <div class="mt-6 p-4">
        {{ $users->appends(request()->query->all())->links() }}
    </div>
@endsection

@section('scripts')
@endsection
