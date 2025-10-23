@extends('layouts.master')

@section('title')
{{ __('vocab.management') }} {{ __('vocab.misconduct_acts') }}
@endsection

@section('main-page')
<a href="{{ route('dashboard') }}">{{ __('vocab.dashboard') }} </a>
@endsection

@section('page')
{{ __('vocab.management') }} {{ __('vocab.misconduct_acts') }}
@endsection

@section('content')

<div class="d-flex justify-content-end m-2 card-body">

    <form action="{{ route('misconduct_acts.destroyAll') }}" method="POST" class="mr-2">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn bg-danger"
            onclick="return confirm('{{ __('vocab.are_you_sure') }}')">
            <i class="fa-solid fa-trash-can"></i> {{ __('vocab.delete_unused_misconduct_acts') }}
        </button>
    </form>


    <a href="{{ route('misconduct_acts.create') }}" class="btn bg-gray">
        <i class="fas fa-plus"></i> {{ __('vocab.new') }}
    </a>
</div>

<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 70%;">{{ __('vocab.misconduct_act') }}</th>
                <th>{{ __('vocab.actions') }}</th>
            </tr>
        </thead>
        <tbody>

            @forelse ($misconduct_acts as $misconduct_act)
            <x-misconduct_act-row :misconduct_act="$misconduct_act" />
            @empty
            <tr>
                <td colspan="3">{{ __('vocab.empty') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('scripts')


@endsection