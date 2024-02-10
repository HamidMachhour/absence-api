@props(['classe'])

<x-card>
    <div class="d-flex flex-wrap justify-content-lg-center">
        <div class="col-sm-6 mb-2">
            <div class="position-relative">
                <a href="{{ route('classeImages.index', ['classe' => $classe->id]) }}">
                    <img src="{{ $classe->classeImage->uri ?? asset('/images/no-image.png') }}" alt="Photo 1"
                        class="img-fluid">
                </a>
                <div class="ribbon-wrapper ribbon">
                    <div class="ribbon bg-success">
                        <a href="{{ route('classes.show', ['classe' => $classe->id]) }}">
                            {{ $classe->name }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <x-classe-tags :tagsCsv="$classe->tags" />


        <div class="user-panel pb-3 d-flex">
            <div class="image">
                <img src="{{ $classe->user->userImage->uri ?? asset('/images/no-image.png') }}"
                    class="img-circle elevation-2"
                    alt="{{ $username = $classe->user->firstName . ' ' . $classe->user->lastName }}">
            </div>
            <div class="info">
                <a href="{{ request()->fullUrlWithQuery(['userId' => $classe->userId, 'page' => 1]) }}"
                    class="d-block">{{ $username }}</a>
            </div>
        </div>
    </div>
    @auth
        @if (auth()->user()->hasRole('admin') || auth()->user()->id === $classe->userId)
            <x-classeactions-card :classe="$classe" />
        @elseif(auth()->user()->isStudentCandidate())
            <x-classestudentactions-card :classe="$classe" />
        @else
            <x-classepublicactions-card :classe="$classe" />
        @endif
    @else
        <x-classepublicactions-card :classe="$classe" />
    @endauth
</x-card>
