@extends('layouts.master')

@section('title')
{{__('vocab.platform')}} {{__('vocab.absence')}} 
@endsection

@section('css')

@endsection

@section('main-page')
<a href="{{route('home')}}">{{__('vocab.home')}}</a>
@endsection
@section('page')
{{__('vocab.new_account')}}
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
                        <h3 class="card-title "> {{__('vocab.login')}} </h3>
                    </div>



                </div>
            </div>
        </div>
        <!--/.col (left) -->
        <!-- right column -->
        <div class="col-md-6">

        </div>
        <!--/.col (right) -->
    </div>

    </div>

    <x-flash-message />
@endsection

@section('scripts')

@endsection
