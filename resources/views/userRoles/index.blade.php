@extends('layouts.master')

@section('title')
{{ __('vocab.roles') }} {{ $user->fullName() }}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('users.show', ['user' => $user]) }}">{{ $user->fullName() }} </a>
@endsection

@section('page')
{{ __('vocab.roles') }} {{ $user->fullName() }}
@endsection

@section('content')
<x-userinfo-card :user="$user" />
@unless ($userRoles->count() == 0)
@auth
@if (auth()->user()->hasRole('admin'))
<div class=' mr-2'>
    <a href="{{ route('userRoles.create', ['user' => $user->id]) }}" class="btn btn-app bg-gray">
        <i class="fas fa-plus"></i> {{__('vocab.new_role')}}
    </a>
</div>

<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{__('vocab.date')}}</th>
                <th>{{__('vocab.role')}}</th>
                <th>
                    <div class="btn-group gap-2">
                        <form action="{{ route('userRoles.destroyAll', ['user' => $user]) }}" method="POST"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-block bg-danger"
                                onclick="return confirm('{{ __('vocab.are_you_sure') }}')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userRoles as $userRole)
            <x-userrole-row :userRole="$userRole" />
            @endforeach
        </tbody>

    </table>
</div>
@endif
@else
@endauth
@else
@if (auth()->user()->hasRole('admin'))
<div>
    <a href="{{ route('userRoles.create', ['user' => $user->id]) }}" class="btn btn-app bg-gray">
        <i class="fas fa-plus"></i> {{__('vocab.new_role')}}
    </a>
</div>
@endif
<p>{{__('vocab.empty')}}</p>
@endunless

@endsection

@section('scripts')
@endsection