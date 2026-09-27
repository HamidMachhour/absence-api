@extends('layouts.master')

@section('title')
{{ __('vocab.management') }} {{ __('vocab.subjects') }}
@endsection

@section('main-page')
<a href="{{ route('dashboard') }}">{{ __('vocab.dashboard') }} </a>
@endsection

@section('page')
{{ __('vocab.management') }} {{ __('vocab.subjects') }}
@endsection

@section('content')

@if (auth()->user()->hasRole('admin'))

<div class="d-flex justify-content-end m-2 card-body">

    <form action="{{ route('subjects.destroyAll') }}" method="POST" class="mr-2">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn bg-danger"
            onclick="return confirm('{{ __('vocab.are_you_sure') }}')">
            <i class="fa-solid fa-trash-can"></i> {{ __('vocab.delete_unused_subjects') }}
        </button>
    </form>
    
 
    <a href="{{ route('subjects.create') }}" class="btn bg-gray">
        <i class="fas fa-plus"></i> {{ __('vocab.new_subject') }}
    </a>
</div>

<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('vocab.date') }}</th>
                <th>{{ __('vocab.subject') }}</th>
                <th>{{ __('vocab.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            
            @forelse ($subjects as $subject)
            <x-subject-row :subject="$subject" />
            @empty
            <tr>
                <td colspan="3">{{ __('vocab.no_data') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@else


<p>{{ __('vocab.not_authorized') }}</p>

@endif

@endsection

@section('scripts')


@endsection