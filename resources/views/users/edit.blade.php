@extends('layouts.master')

@section('title')
{{__('vocab.update').' '.__('vocab.account')}}
@endsection

@section('css')
@endsection

@section('preload')
@endsection
@section('main-page')
<a href="{{ route('home') }}">{{__('vocab.home')}}</a>
@endsection

@section('page-title')
{{__('vocab.home')}}
@endsection

@section('page')
{{__('vocab.update').' '.__('vocab.account')}}
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">

                        <div class="card-title">{{__('vocab.update').' '.__('vocab.account')}}</div>
                        @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="card-body">

                        <form method="POST" action="{{ route('users.update', ['user' => $user->id]) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group ">
                                <label for="firstName" class="inline-block   mb-2"> {{__('vocab.last_name')}} </label>
                                <input type="text" class=" form-control"
                                    name="firstName" value="{{ $user->firstName }}" id="firstName" />

                                @error('firstName')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group ">
                                <label for="lastName"> {{__('vocab.first_name')}} </label>
                                <input type="text" class=" form-control"
                                    name="lastName" value="{{ $user->lastName }}" id="lastName" />

                                @error('lastName')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group ">
                                <label for="firstNameZ" class="inline-block   mb-2"> {{__('vocab.last_name_z')}} </label>
                                <input type="text" class=" form-control"
                                    name="firstNameZ" value="{{ $user->firstNameZ }}" id="firstNameZ" />

                                @error('firstNameZ')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group ">
                                <label for="lastNameZ"> {{__('vocab.first_name_z')}} </label>
                                <input type="text" class=" form-control"
                                    name="lastNameZ" value="{{ $user->lastNameZ }}" id="lastNameZ" />

                                @error('lastNameZ')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group ">
                                <label for="firstNameFr" class="inline-block   mb-2"> {{__('vocab.last_name_fr')}} </label>
                                <input type="text" class=" form-control"
                                    name="firstNameFr" value="{{ $user->firstNameFr }}" id="firstNameFr" />

                                @error('firstNameFr')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group ">
                                <label for="lastNameFr"> {{__('vocab.first_name_fr')}} </label>
                                <input type="text" class=" form-control"
                                    name="lastNameFr" value="{{ $user->lastNameFr }}" id="lastNameFr" />

                                @error('lastNameFr')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group ">
                                <label for="firstNameEn" class="inline-block   mb-2"> {{__('vocab.last_name_en')}} </label>
                                <input type="text" class=" form-control"
                                    name="firstNameEn" value="{{ $user->firstNameEn }}" id="firstNameEn" />

                                @error('firstNameEn')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group ">
                                <label for="lastNameEn"> {{__('vocab.first_name_en')}} </label>
                                <input type="text" class=" form-control"
                                    name="lastNameEn" value="{{ $user->lastNameEn }}" id="lastNameEn" />

                                @error('lastNameEn')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group ">
                                <label for="code"> {{__('vocab.code')}} </label>
                                <input type="text" class=" form-control"
                                    name="code" value="{{ $user->code }}" id="code" />

                                @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group ">
                                <label for="tag_id"> {{__('vocab.tag_id')}} </label>
                                <input type="text" class=" form-control"
                                    name="tag_id" value="{{ $user->tag_id }}" id="tag_id" />

                                @error('tag_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>



                            <div class="form-group ">
                                <input type="checkbox" id="change_password" name="change_password" class="mr-2" />
                                <label for="change_password">{{__('vocab.change_password')}}</label>
                            </div>
                            <div class="password-fields mb-6" style="display: none;">
                                <div class="form-group ">
                                    <label for="old_password">
                                        {{__('vocab.current_password')}}
                                    </label>
                                    <input type="password" class="form-control  "
                                        name="old_password" value="{{ old('old_password') }}" />

                                    @error('old_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="password">
                                        {{__('vocab.new_password')}}
                                    </label>
                                    <input type="password" class="form-control  "
                                        name="password" value="{{ old('password') }}" />

                                    @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group ">
                                    <label for="password_confirmation">
                                        {{__('vocab.confirm_password')}}
                                    </label>
                                    <input type="password" class="form-control  "
                                        name="password_confirmation" value="{{ old('password_confirmation') }}" />

                                    @error('password_confirmation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer ">
                                <button type="submit" class="btn  btn-outline-success">
                                    <i class="fa-solid fa-save"></i>
                                    {{__('vocab.save')}}
                                </button>

                                <a href="{{ route('users.show', ['user' => $user->id]) }}" class=" btn btn-outline-primary">
                                    <i class="fa-solid fa-circle-arrow-left"></i>
                                    {{__('vocab.go_back')}}
                                </a>

                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    const checkbox = document.getElementById('change_password');
    const passwordFields = document.querySelector('.password-fields');

    checkbox.addEventListener('change', function() {
        passwordFields.style.display = this.checked ? 'block' : 'none';
    });
</script>
@endsection