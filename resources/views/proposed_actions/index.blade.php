@extends('layouts.master')

@section('title')
{{ __('vocab.management') }} {{ __('vocab.proposed_actions') }}
@endsection

@section('main-page')
<a href="{{ route('dashboard') }}">{{ __('vocab.dashboard') }} </a>
@endsection

@section('page')
{{ __('vocab.management') }} {{ __('vocab.proposed_actions') }}
@endsection

@section('content')

<div class="d-flex justify-content-end  m-2 card-body">

    <form action="{{ route('proposed_actions.destroyAll') }}" method="POST" class="mr-2">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn bg-danger"
            onclick="return confirm('{{ __('vocab.are_you_sure') }}')">
            <i class="fa-solid fa-trash-can"></i> {{ __('vocab.delete_unused_proposed_actions') }}
        </button>
    </form>


    <a href="{{ route('proposed_actions.create') }}" class="btn bg-gray">
        <i class="fas fa-plus"></i> {{ __('vocab.new') }}
    </a>
</div>

<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="width: 70%;">{{ __('vocab.proposed_action') }}</th>
                <th>{{ __('vocab.actions') }}</th>
            </tr>
        </thead>
        <tbody>

            @forelse ($proposed_actions as $proposed_action)
            <x-proposed_action-row :proposed_action="$proposed_action" />
            @empty
            <tr>
                <td colspan="3">{{ __('vocab.no_data') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('scripts')


@endsection