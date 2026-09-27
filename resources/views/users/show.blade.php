@extends('layouts.master')

@section('title')
{{__('vocab.profile')}}
@endsection

@section('css')
@endsection

@section('preload')
@endsection
@section('main-page')
<a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
{{__('vocab.profile')}}
@endsection

@section('page')
{{__('vocab.profile')}}
@endsection

@section('content')
<!-- Profile Image -->
<div class="card card-primary card-outline mx-4">
    <div class="card-body box-profile">
        <div class="text-center">
            <a href="{{ route('userImages.show', ['user' => $user->id]) }}">
                <img class="profile-user-img img-fluid img-circle"
                    src="{{ $user->userImage->uri ?? asset('images/no-image.png') }}">
            </a>
        </div>

        <h3 class="profile-username text-center">{{ $user->fullName() }}</h3>

        <p class="text-muted text-center">{{ $user->localRole() }}</p>

        <ul class="list-group list-group-unbordered mb-3">

            <li class="list-group-item">
                <a class="float-right">{{ $user->email }}</a>
            </li>
            @php
            $student = $user->students()->latest()->first();
            $classe = $student ? $student->classe : null;
            @endphp
            @if(isset($student))
            <li class="list-group-item">
                <b class="float-right">{{__('vocab.class')}}</b>
                <a href="{{ route('students.index', ['classeId' => optional($classe)->id]) }}" class="float-left">
                    {{ optional($classe)->classeName() ?? '' }}
                </a>
            </li>
            @endif
        </ul>

        @auth
        <div class="btn-group mx-1">
            @if (auth()->user()->id === $user->id || auth()->user()->hasRole('admin'))
            @if (isset($student))
            <a class="btn btn-app bg-warning mx-1"
                href="{{ route('students.card', ['student' => $student->id]) }}">
                <i class="far fa-address-card"></i> {{__('vocab.card')}}
            </a>
            @endif
            <a href="{{ route('users.edit', ['user' => $user->id]) }}" class="btn btn-app bg-info mx-1">
                <i class="fas fa-edit"></i>
                {{__('vocab.edit')}}
            </a>
            @if (auth()->user()->hasRole('admin'))
            <form action="{{ route('users.destroy', ['user' => $user->id]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-app bg-danger mx-1"
                    onclick="return confirm('{{ __('vocab.delete_permanently') . '?' }}')">
                    <i class="fas fa-trash"></i>
                    {{__('vocab.delete')}}
                </button>
            </form>
            <form action="{{ route('userRoles.index',$user) }}" class="inline" method="GET">
                <button type="submit" class="btn btn-app bg-gray ">
                    <i class="fas fa-user"></i>
                    {{__('vocab.roles')}}
                </button>
            </form>
            @endif
            <form class="inline" method="POST"
                action="{{ route('users.logout', ['user' => $user->id]) }}">
                @csrf
                <input type="hidden" name="logout_all_devices" value="1">

                <button type="submit" class="btn btn-app mx-1">
                    <i class="fas fa-door-closed "></i> {{__('vocab.logout')}} {{__('vocab.all_devices')}}
                </button>
            </form>
            @else
            @endif
        </div>
        @endauth
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->
@endsection

@section('scripts')
@endsection