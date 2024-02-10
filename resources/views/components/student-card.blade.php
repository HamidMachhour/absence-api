@props(['student'])
<x-card>
    <div class="grid grid-cols-4 gap-1 mb-2">
        <div class="d-flex flex-wrap">
            <a href="{{ route('userImages.show', ['user' => $student->userId]) }}">
                <img class="rounded-xl w-20" src="{{ $student->user->userImage->uri ?? asset('/images/no-image.png') }}"
                    alt="" />
            </a>
        </div>
        <div class="d-flex flex-wrap col-span-2">
            <h3>
                <a href="{{ request()->fullUrlWithQuery(['userId' => $student->userId, 'page' => 1]) }}"
                    class="text-green-600 hover:text-orange-700">
                    {{ $student->num }}
                    <span> - <span>
                            {{ $student->user->firstName }}
                            <span>
                                {{ $student->user->lastName }}
                </a>
            </h3>
            <div class="flex flex-wrap bg-orange-200 justify-center m-1 p-1 text-xs rounded-xl">
                {{ $student->user->email }}
            </div>
            <div class="flex flex-wrap bg-green-200 justify-center m-1 p-1 text-xs rounded-xl">
                {{ 'رقم: ' . $student->user->code }}
            </div>
            <div class="flex flex-wrap items-center mt-1 text-xs">
                <a href="{{ request()->fullUrlWithQuery(['maxAbsences' => ($nbAbsences = $student->absences_count ?? 0), 'page' => 1, 'orderBy' => 'absences_count desc']) }}"
                    class="flex items-center justify-center bg-gray-200 hover:opacity-80 text-green-600 rounded-xl m-1 p-1">
                    <i class="fa-solid fa-arrow-down"></i>
                </a>
                <div class=" flex bg-gray-200 items-center text-orange-600 rounded-xl m-1 p-1 gap-1">
                    <div class=" text-green-700  text-xs">
                        حالات غياب
                    </div>
                    <div>
                        {{ $nbAbsences }}
                    </div>
                </div>
                <a href="{{ request()->fullUrlWithQuery(['minAbsences' => $nbAbsences, 'page' => 1, 'orderBy' => 'absences_count']) }}"
                    class="flex items-center justify-center bg-gray-200 hover:opacity-80 text-green-600 rounded-xl m-1 p-1">
                    <i class="fa-solid fa-arrow-up"></i>
                </a>
            </div>
            <div class="flex flex-wrap items-center mt-1 text-xs">
                <a href="{{ request()->fullUrlWithQuery(['maxPlage' => ($sumPlage = $student->absences_sum_plage ?? 0), 'page' => 1, 'orderBy' => 'absences_sum_plage desc']) }}"
                    class="flex items-center justify-center bg-gray-200 hover:opacity-80 text-green-600 rounded-xl m-1 p-1">
                    <i class="fa-solid fa-arrow-down"></i>
                </a>
                <div class=" flex items-center bg-gray-200 text-orange-600 rounded-xl m-1 p-1 gap-1">
                    <div class=" text-green-700  text-xs">
                        ساعات غياب
                    </div>
                    <div>
                        {{ round($sumPlage/60,2) }}
                    </div>
                </div>
                <a href="{{ request()->fullUrlWithQuery(['minPlage' => $sumPlage, 'page' => 1, 'orderBy' => 'absences_sum_plage']) }}"
                    class="flex items-center justify-center bg-gray-200 hover:opacity-80 text-green-600 rounded-xl m-1 p-1">
                    <i class="fa-solid fa-arrow-up"></i>
                </a>
            </div>

            <div class="flex">
                <a class="flex items-center gap-2 bg-orange-200 hover:opacity-80 text-gray-600 rounded-xl p-1 m-1 text-xs"
                    href="{{ request()->fullUrlWithQuery(['classeId' => $student->classeId, 'page' => 1]) }}">
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
                <x-studentactions-card :student="$student" />
            {{-- @else
                <x-studentpublicactions-card :student="$student" /> --}}
            @endif
            {{--  @else
            <x-studentpublicactions-card :student="$student" /> --}}
        @endauth

    </div>
</x-card>
