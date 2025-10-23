@props(['user'])

<x-card :title='$user->fullName()'>
    <div class="user-panel d-flex">
        <div class="image">
            <a href="{{ route('users.show', ['user' => $user->id]) }}">
                <img class="img-circle elevation-1" src="{{ $user->userImage->uri ?? asset('/assets/images/no-image.png') }}"
                    alt="" />
            </a>
        </div>
        <div class="info">
            <a href="{{ route('users.show', ['user' => $user->id]) }}" class="text-gray-700 hover:text-blue-600">
                {{ $user->fullName()}}
            </a>
            @isset($user->email)
                <div class="flex  items-center text-blue-600 hover:opacity-80">
                    <a href="mailto:{{ $user->email }}">
                        <i class="fa-solid fa-envelope"></i>
                        {{ $user->email }}</a>
                </div>
            @endisset
            @isset($user->address)
                <x-address-phones :phonesCsv="$user->address->phones" />
            @endisset
        </div>
    </div>
</x-card>
