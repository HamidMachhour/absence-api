@props(['user'])

<x-card>
    <div class="grid gap-1 mb-2 text-xs">

        <div class="flex justify-center gap-1 mb-2">

            <div class="d-flex flex-column text-right">
                <h3 class="text-xs">
                    <a href="{{ route('users.show', ['user' => $user->id]) }}"
                        class="text-green-600 hover:text-orange-700">
                        {{ $user->firstName . ' ' . $user->lastName }}
                    </a>
                </h3>
                @isset($user->email)
                    <div class="flex  items-center text-gray-700 hover:opacity-80">
                        <a href="mailto:{{ $user->email }}">
                            <i class="fa-solid fa-envelope"></i>
                            {{ $user->email }}</a>
                    </div>
                @endisset

                @isset($user->email_verified_at)
                    <div class="flex gap-2 items-center text-orange-700 justify-end">
                        <a href="{{ request()->fullUrlWithQuery(['verified' => 'yes', 'page' => 1]) }}"
                            class="flex text-blue-600 hover:text-green-700 justify-center">
                            حساب محقق
                        </a>
                    </div>
                @else
                    <div class="flex gap-2 items-center text-orange-700 justify-end">
                        <a href="{{ request()->fullUrlWithQuery(['verified' => 'no', 'page' => 1]) }}"
                            class="flex text-red-600 hover:text-green-700 justify-center">
                            حساب غير محقق
                        </a>
                    </div>
                @endisset

                @isset($user->roles)
                    <x-user-roles :user_roles="$user->roles" />
                @endisset

                @isset($user->address)
                    <x-address-phones :phonesCsv="$user->address->phones" />
                @endisset
            </div>
            <div class="d-flex flex-wrap">
                <a href="{{ route('userImages.show', ['user' => $user->id]) }}">
                    <img class="rounded-xl w-20" src="{{ $user->userImage->uri ?? asset('/images/no-image.png') }}"
                        alt="" />
                </a>
            </div>
        </div>
        {{-- <x-seanceactions-card :user="$user" /> --}}
    </div>
</x-card>
