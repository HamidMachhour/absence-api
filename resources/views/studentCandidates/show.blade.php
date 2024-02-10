<x-layout>
    <x-classeinf-card :classe="$student->classe" />

    <div class="mx-4">
        <x-card class="p-10">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="flex flex-row items-center justify-center">
                    <div class="d-flex flex-column">
                        <a href="{{ route('userImages.index', ['student' => $student->id]) }}">
                            <img class="rounded-xl w-48" src="{{ $student->studentImage->uri ?? asset('/images/no-image.png') }}"
                                alt="" />
                        </a>
                    </div>
                    @auth
                        @if (auth()->user()->hasRole('admin') || auth()->user()->id === $student->classe->userId)
                            <x-studentcandidateactions-card :student="$student" />
                        @endif
                    @endauth
                </div>
                <div>
                    <h3 class="text-3xl font-bold mb-4">الاسم الكامل</h3>
                    <div class="text-lg space-y-6">
                        {{ $student->user->firstName . ' ' . $student->user->lastName }}
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layout>
