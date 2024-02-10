@props(['user'])

<x-card>
    <div class="flex justify-center gap-1 mb-2">

        <div class="d-flex flex-column text-right">
            <h3 class="text-xl">
                <a href="{{ route('users.show', ['user' => $user->id]) }}" class="text-green-600 hover:text-orange-700">
                    {{ $user->firstName . ' ' . $user->lastName }}
                </a>
            </h3>
            @isset($user->email)
                <div class="flex  items-center text-orange-700 hover:opacity-80">
                    <a href="mailto:{{ $user->email }}">
                        <i class="fa-solid fa-envelope"></i>
                        {{ $user->email }}</a>
                </div>
            @endisset
            @isset($user->address)
                <x-address-phones :phonesCsv="$user->address->phones" />
            @endisset
            {{-- <div class="flex  items-center text-green-700 hover:opacity-80">
                <a href="tel:{{ $classe->address->phones ?? 'no phone' }}"><i class="fa-solid fa-phone"></i>
                    {{ $user->address->phones ?? 'no phone' }}</a>
            </div> --}}
        </div>
        <div class="d-flex flex-wrap">
            <a href="{{ route('userImages.show', ['user' => $user->id]) }}">
                <img class="rounded-xl w-20" src="{{ $user->userImage->uri ?? asset('/images/no-image.png') }}"
                    alt="" />
            </a>
        </div>
    </div>
</x-card>
