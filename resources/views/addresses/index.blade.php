@extends('layouts.master')

@section('title')
    {{__('vocab.my_addresses')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('users.dashboard', ['user', auth()->user()]) }}"> {{__('vocab.dashboard')}}</a>
@endsection

@section('page')
    {{__('vocab.my_addresses')}}
@endsection

@section('content')

<x-filters-card :params="$params" />

    <div class=' mr-2'>
        <a href="{{ route('addresses.create') }}" class="btn btn-app bg-gray">
            <i class="fas fa-plus"></i>{{__('vocab.new_address')}}
        </a>
    </div>

    <div class="lg:grid grid-cols-3 gap-1 mx-1">

        @unless (count($addresses) == 0)
            @foreach ($addresses as $address)
                <x-address-card :address="$address" />
            @endforeach
        @else
            <p>{{__('vocab.no_data')}}</p>
        @endunless
    </div>

    <div class="mt-6 p-4">
        {{ $addresses->appends(request()->query->all())->links() }}
    </div>
@endsection

@section('scripts')
@endsection
