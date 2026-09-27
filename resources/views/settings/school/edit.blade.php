@extends('layouts.master')

@section('title')
{{__('vocab.school_settings')}}
@endsection

@section('css')
{{-- Add any specific CSS for forms or layout here --}}
@endsection

@section('main-page')
<a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
@endsection

@section('page')
{{__('vocab.edit_school_config')}}
@endsection

@section('content')
<div class="d-flex justify-content-center align-items-center">
    <div class="card card-primary">
        <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
            <h3 class="card-title">{{__('vocab.school_configuration')}}</h3>
        </div>

        {{-- Use the PUT method to update the single settings row --}}
        <form method="POST" action="{{ route('settings.school.update') }}">
            @csrf
            @method('PUT')

            @if (session('success'))
            <div class="alert alert-success mt-3 mx-3">{{ session('success') }}</div>
            @endif

            <div class='card-body'>

                {{-- CORE IDENTITY SECTION --}}
                <h5 class="text-primary mb-3">{{__('vocab.identity')}}</h5>

                {{-- School Name (Arabic) --}}
                <div class="form-group">
                    <label for="name"> {{__('vocab.school_name')}} ({{__('vocab.arabic')}})</label>
                    <input id='name' type="text" class="form-control" name="name"
                        value="{{ old('name', $config?->name ?? '') }}" />
                    @error('name')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- School Name (French) --}}
                <div class="form-group">
                    <label for="name_fr"> {{__('vocab.school_name')}} ({{__('vocab.french')}})</label>
                    <input id='name_fr' type="text" class="form-control" name="name_fr"
                        value="{{ old('name_fr', $config?->name_fr ?? '') }}" />
                    @error('name_fr')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- School Name (English) --}}
                <div class="form-group">
                    <label for="name_en"> {{__('vocab.school_name')}} ({{__('vocab.english')}})</label>
                    <input id='name_en' type="text" class="form-control" name="name_en"
                        value="{{ old('name_en', $config?->name_en ?? '') }}" />
                    @error('name_en')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- School Name (Tifinagh) --}}
                <div class="form-group">
                    <label for="name_z"> {{__('vocab.school_name')}} ({{__('vocab.tamazight')}})</label>
                    <input id='name_z' type="text" class="form-control" name="name_z"
                        value="{{ old('name_z', $config?->name_z ?? '') }}" />
                    @error('name_z')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- School Code --}}
                <div class="form-group">
                    <label for="code">{{__('vocab.code')}} </label>
                    <input id='code' type="text" class="form-control" name="code"
                        value="{{ old('code', $config?->code ?? '') }}" />
                    @error('code')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City and Address --}}
                <div class="form-group">
                    <label for="city">{{__('vocab.city')}} </label>
                    <input id='city' type="text" class="form-control" name="city"
                        value="{{ old('city', $config?->city ?? '') }}" />
                    @error('city')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">{{__('vocab.address')}} </label>
                    <input id='address' type="text" class="form-control" name="address"
                        value="{{ old('address', $config?->address ?? '') }}" />
                    @error('address')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Logo Path --}}
                <div class="form-group">
                    <label for="logo_path">{{__('vocab.logo_path')}} </label>
                    <input id='logo_path' type="text" class="form-control" name="logo_path"
                        value="{{ old('logo_path', $config?->logo_path ?? '') }}"
                        placeholder="https://example.com/path/to/logo.png" />
                    @error('logo_path')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <hr class="my-4">

                {{-- CONTACT & ADMIN SECTION --}}
                <h5 class="text-primary mb-3">{{__('vocab.contact_info')}}</h5>

                <div class="form-group">
                    <label for="director_name">{{__('vocab.director_name')}} </label>
                    <input id='director_name' type="text" class="form-control" name="director_name"
                        value="{{ old('director_name', $config?->director_name ?? '') }}" />
                    @error('director_name')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone_number">{{__('vocab.phone_number')}} </label>
                    <input id='phone_number' type="text" class="form-control" name="phone_number"
                        value="{{ old('phone_number', $config?->phone_number ?? '') }}" />
                    @error('phone_number')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">{{__('vocab.email')}} </label>
                    <input id='email' type="email" class="form-control" name="email"
                        value="{{ old('email', $config?->email ?? '') }}" />
                    @error('email')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Academic Year --}}
                <hr class="my-4">
                <h5 class="text-primary mb-3">{{__('vocab.academic_year')}}</h5>

                <div class="form-group">
                    <label for="current_academic_start">{{__('vocab.academic_start_date')}} </label>
                    <input id='current_academic_start' type="date" class="form-control" name="current_academic_start"
                        value="{{ old('current_academic_start', $config?->current_academic_start?->format('Y-m-d') ?? '') }}" />
                    @error('current_academic_start')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="current_academic_end">{{__('vocab.academic_end_date')}} </label>
                    <input id='current_academic_end' type="date" class="form-control" name="current_academic_end"
                        value="{{ old('current_academic_end', $config?->current_academic_end?->format('Y-m-d') ?? '') }}" />
                    @error('current_academic_end')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Logo path field would typically be a file input, not a text input --}}
                {{-- <div class="form-group">...</div> --}}

                <hr class="my-4">
                <h5 class="text-primary mb-3">{{__('vocab.system_settings')}}</h5>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="process_attendances_enabled"
                            name="process_attendances_enabled"
                            {{ old('process_attendances_enabled', $config?->process_attendances_enabled ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="process_attendances_enabled">
                            {{ __('vocab.enable_automatic_attendance_processing') }}
                        </label>
                    </div>
                    @error('process_attendances_enabled')
                    <p class="text-danger text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-outline-primary">
                    {{__('vocab.save')}}
                </button>
            </div>
        </form>
    </div>
</div>
<x-flash-message />
@endsection

@section('scripts')
{{-- Any specific JS required for date pickers, etc., goes here --}}
@endsection