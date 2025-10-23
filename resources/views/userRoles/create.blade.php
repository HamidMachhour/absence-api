@extends('layouts.master')

@section('title')
{{__('vocab.new')}} {{__('vocab.role')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('userRoles.index',['user'=>$user]) }}">{{__('vocab.roles')}}</a>
@endsection

@section('page-title')
{{__('vocab.new')}} {{__('vocab.role')}}
@endsection

@section('page')
{{__('vocab.new')}} {{__('vocab.role')}}
@endsection
@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"> {{__('vocab.new')}} {{__('vocab.role')}} </h3>
                        @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @csrf
                    <div class='card-body'>

                        <form method="POST" action="{{ route('userRoles.store',['user'=>$user]) }}">

                            <div class="form-group ">
                                <select name="roleId" class="form-control" >
                                    <option value=""> {{__('vocab.role')}}
                                    </option>
                                    @foreach ($roles as $role)
                                    <option value="{{$role->id}}" {{ request()->get('roleId') === $role->id ? 'selected' : '' }}>{{ $role->roleName()}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card-footer ">
                                <a href="javascript:history.back()" class="btn btn-outline-gray">
                                    {{__('vocab.go_back') }}</a>

                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-outline-primary">
                                    {{__('vocab.save')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-flash-message />
    @endsection

    @section('scripts')
    @endsection