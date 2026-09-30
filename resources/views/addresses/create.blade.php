@extends('layouts.master')

@section('title')
    {{__('vocab.new_address')}}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('addresses.index') }}">{{__('vocab.my_addresses')}}</a>
@endsection
@section('page')
    {{__('vocab.new_address')}}
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
                            <div class="card-title h2"> {{__('vocab.new_address')}}
                            </div>
                        </div>
                        <div class="card-body">

                            <form method="POST" action="{{ route('addresses.store') }}">
                                @csrf
                                <div class="form-group ">
                                    <label for="strAddress">{{__('vocab.street')}} </label>
                                    <input type="text" class="form-control " name="strAddress"
                                        placeholder="{{__('vocab.street')}}  " value="{{ old('strAddress') }}" />

                                    @error('strAddress')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label for="city">{{__('vocab.city')}}</label>
                                    <input type="text" class="form-control " name="city" />

                                    @error('city')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group ">
                                    <label for="zipCode">{{__('vocab.zip_code')}}</label>
                                    <input type="number" step="1" value="1" class="form-control "
                                        name="zipCode" />

                                    @error('zipCode')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="country">{{__('vocab.country')}}</label>
                                    <input type="text" class="form-control " name="country" />
                                    @error('country')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="location">{{__('vocab.xycoord')}}</label>
                                    <input type="text" class="form-control " name="location" />
                                    @error('location')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="phones">
                                        {{__('vocab.phone_numbers')}}
                                    </label>
                                    <input type="text" class="form-control " name="phones"
                                        value="{{ old('phones') }}" placeholder="+21212131415, 066000444,..." />

                                    @error('phones')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="email">
                                        {{__('vocab.email')}}
                                    </label>
                                    <input type="email" class="form-control " name="email" rows="10"
                                        value="{{ old('email') }}" />

                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="card-footer ">
                                    <button type="submit" class="btn  btn-outline-success">
                                        <i class="fa-solid fa-save"></i>
                                        {{__('vocab.add')}}
                                    </button>
                                    <a href="javascript:history.back()" class=" btn btn-outline-primary">
                                        <i class="fa-solid fa-circle-arrow-left"></i>
                                        {{__('vocab.go_back')}}
                                    </a>
                                </div>
                            </form>
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
