@extends('layouts.master')

@section('title')
    {{__('vocab.class')}} {{ $classe->classeName() }}
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('classes.show',['classe'=>$classe]) }}">{{$classe->classeName()}} {{__('vocab.class')}}</a>
@endsection

@section('page')
{{__('vocab.classe_images')}}
@endsection

@section('content')
    <x-classeinf-card :classe="$classe" />
    @unless ($classe_images->count() == 0)
        @auth
            @if (auth()->user()->hasRole('admin') || $classe->userId === auth()->user()->id)
                <div class=' mr-2'>
                    <a href="{{ route('classeImages.create', ['classe' => $classe->id]) }}" class="btn btn-app bg-gray">
                        <i class="fas fa-plus"></i> {{__('vocab.add_image')}}
                    </a>
                </div>
                <x-card :title='__("vocab.images")'>
                <div class="lg:grid lg:grid-cols-2 gap-1 mx-1">
                    @foreach ($classe_images as $classe_image)
                        <div class="col-sm-12">
                            <div class="position-relative">
                                <img src="{{ $classe_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                                    class="img-fluid rounded-xl">
                            </div>
                            <div class="position-absolute right-1 top-0">
                                <form action="{{ route('classeImages.destroy', ['image' => $classe_image->id]) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-block bg-danger" onclick="return confirm('{{__('vocab.are_you_sure')}}')">
                                        <i class="fas fa-cancel"></i>
                                    </button>
                                </form>
                                @if ($classe->imageId !== $classe_image->id)
                                    <form
                                        action="{{ route('classeImages.setDefault', ['classe' => $classe->id, 'image' => $classe_image->id]) }}"
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
                    @foreach ($classe_images as $classe_image)
                        <div class="position-relative">
                            <img src="{{ $classe_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                                class="img-fluid rounded-xl">
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="lg:grid lg:grid-cols-2 gap-2 mx-2">
                @foreach ($classe_images as $classe_image)
                    <div class="position-relative">
                        <img src="{{ $classe_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                            class="img-fluid rounded-xl">
                    </div>
                @endforeach
            </div>
        @endauth
        <div class="mt-6 p-4">
            {{ $classe_images->appends(request()->query->all())->links() }}
        </div>
    @else
        @if (auth()->user()->hasRole('admin') || $classe->userId === auth()->user()->id)
            <div>
                <a href="{{ route('classeImages.create', ['classe' => $classe->id]) }}" class="btn btn-app bg-gray">
                    <i class="fas fa-plus"></i> {{__('vocab.add_image')}}
                </a>
            </div>
        @endif
        <p>لا يوجد شيء هنا</p>
    @endunless
@endsection

@section('scripts')
@endsection
