@props(['student'])
<x-card>
    <div class="grid grid-cols-4 gap-1 mb-2">
        <div class="d-flex flex-column">
            <a href="{{ route('userImages.show', ['user' => $student->userId]) }}">
                <img class="rounded-xl w-48" src="{{ $student->user->userImage->uri ?? asset('/images/no-image.png') }}"
                    alt="" />
            </a>
        </div>
        <div class="d-flex flex-column col-span-2">
            <h3 class="text-xl">
                <a href="{{ route('users.show', ['user' => $student->userId]) }}"
                    class="text-green-600 hover:text-orange-700">
                    {{ $student->user->firstName }}
                    <span>
                        {{ $student->user->lastName }}
                </a>
            </h3>

            <div class="items-center flex flex-start bg-gray-200 text-orange-600 rounded-xl p-1 m-1 text-xs">
                {{ $student->created_at }}
            </div>

            <div class="flex">
                <a class="flex items-center gap-2 bg-orange-200 hover:opacity-80 text-gray-600 rounded-xl p-2 mb-1 text-xs"
                    href="{{route('candidates.manage', ['classe' => $student->classeId])}}">
                    <img class="w-10 rounded-circle"
                        src="{{ $student->classe->classeImage->uri ?? asset('/images/no-image.png') }}"
                        alt="" />
                    {{ $student->classe->name }}
                </a>
            </div>
        </div>
        @auth
            @if (auth()->user()->id === $student->classe->userId ||
                    auth()->user()->hasRole('admin'))
                <x-studentcandidateactions-card :student="$student" />
            @endif
        @endauth

    </div>
</x-card>
