<x-layout>
    </a>
    <div class="mx-4">
        <x-card class="p-10">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="flex flex-row items-center justify-center">
                    <div class="d-flex flex-column">
                        <a href="{{ route('classeImages.index', ['classe' => $classe->id]) }}">
                            <img class="rounded-xl w-48" src="{{ $classe->classeImage->uri ?? asset('/images/no-image.png') }}"
                                alt="" />
                        </a>
                    </div>
                    @auth
                        @if (auth()->user()->hasRole('admin') || auth()->user()->id === $classe->userId)
                            <x-classeactions-card :classe="$classe" />
                        @elseif(auth()->user()->isStudentCandidate())
                            <x-classestudentactions-card :classe="$classe" />
                        @else
                            <x-classepublicactions-card :classe="$classe" />
                        @endif
                    @endauth
                </div>
                <h3 class="text-2xl mb-2">
                    {{ $classe->name }}
                </h3>
                <div class="flex gap-2 items-center text-orange-700 hover:opacity-80">
                    <i class="fa-solid fa-users"></i>
                    <div>العدد:</div>
                    <div>{{ count($classe->students) }}</div>
                </div>
                <div class="flex bg-orange-200 rounded-xl">
                    <a class="flex items-center gap-2  hover:opacity-80 text-gray-600 m-1 text-xs"
                        href="{{ route('users.show',['user' => $classe->userId]) }}">
                        <img class="w-10 rounded-circle"
                            src="{{ $classe->user->userImage->uri ?? asset('/images/no-image.png') }}" alt="" />
                        {{ $classe->user->firstName }}
                        <span>
                            {{ $classe->user->lastName }}
                    </a>
                </div>
                <div class="m-2 border border-gray-200 w-full mb-6"></div>
                <div>
                    <h3 class="text-3xl font-bold mb-4">Description</h3>
                    <div class="text-lg space-y-6">
                        {{ $classe->description }}
                    </div>
                </div>
                <div class="lg:grid lg:grid-cols-2 gap-2 mt-2">

                    @unless ($classe->classeImages->count() == 0)

                        @foreach ($classe->classeImages as $classe_image)
                            <img class="rounded-xl w-full" src="{{ $classe_image->uri ?? asset('/images/no-image.png') }}"
                                alt="" />
                        @endforeach
                    @else
                        <p>No images found</p>
                    @endunless

                </div>
            </div>
        </x-card>
    </div>
</x-layout>
