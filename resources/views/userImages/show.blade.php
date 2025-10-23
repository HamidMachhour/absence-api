@extends('layouts.master')

@section('title')
{{ $user->fullName() }} 
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('users.show', ['user' => $user]) }}">{{ $user->fullName() }} </a>
@endsection

@section('page')
{{__('vocab.profile_images')}}
@endsection

@section('content')
    <x-userinfo-card :user="$user" />
    @unless ($user_images->count() == 0)
        @auth
            @if ($user->id === auth()->user()->id || auth()->user()->hasRole('admin'))
                <div class=' mr-2'>
                    <a href="{{ route('userImages.create', ['user' => $user->id]) }}" class="btn btn-app bg-gray">
                        <i class="fas fa-plus"></i> {{__('vocab.add_image')}}
                    </a>
                </div>
                <x-card :title="__('vocab.images')">
                    <div class="lg:grid lg:grid-cols-2 gap-1 mx-1">
                        @foreach ($user_images as $user_image)
                            <div class="col-sm-12">
                                <div class="position-relative">
                                    <img src="{{ $user_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                                        class="img-fluid rounded-xl">
                                </div>
                                <div class="position-absolute right-1 top-0">
                                    <form action="{{ route('userImages.destroy', ['image' => $user_image->id]) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-block bg-danger"
                                            onclick="return confirm('{{__('vocab.are_you_sure')}}')">
                                            <i class="fas fa-cancel"></i>
                                        </button>
                                    </form>
                                    @if ($user->imageId !== $user_image->id)
                                        <form
                                            action="{{ route('userImages.setDefault', ['user' => $user->id, 'image' => $user_image->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-block bg-success"
                                                onclick="return confirm('{{__('vocab.are_you_sure')}}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div class="btn btn-block bg-gray">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @else
                <div class="lg:grid lg:grid-cols-2 gap-2 mx-2">
                    @foreach ($user_images as $user_image)
                        <div class="position-relative">
                            <img src="{{ $user_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                                class="img-fluid rounded-xl">
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="lg:grid lg:grid-cols-2 gap-2 mx-2">
                @foreach ($user_images as $user_image)
                    <div class="position-relative">
                        <img src="{{ $user_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                            class="img-fluid rounded-xl">
                    </div>
                @endforeach
            </div>
        @endauth
        <div class="mt-6 p-4">
            {{ $user_images->appends(request()->query->all())->links() }}
        </div>
    @else
        @if ($user->id === auth()->user()->id || auth()->user()->hasRole('admin'))
            <div>
                <a href="{{ route('userImages.create', ['user' => $user->id]) }}" class="btn btn-app bg-gray">
                    <i class="fas fa-plus"></i> {{__('vocab.add_image')}}
                </a>
            </div>
        @endif
        <p>{{__('vocab.empty')}}</p>
    @endunless
 
@endsection

@section('scripts')
@endsection
