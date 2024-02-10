@props(['address'])

<x-card>
    <div class="grid grid-cols-4 gap-1 mb-2">
        <div class="d-flex flex-column">
            {{-- for map location --}}
        </div>
        <div class="d-flex flex-column col-span-2 text-xs">
            <h3 class="text-xl">
                <a href="{{ route('addresses.show', ['address' => $address->id]) }}"
                    class="text-green-600 hover:text-orange-700">
                    {{ $address->strAddress }}
                </a>
            </h3>
            <div class="text-orange-600">
                {{ $address->city }}
            </div>
            <div class="text-orange-600">
                {{ $address->zipCode }}
            </div>
            <div class="text-orange-600">
                {{ $address->country }}
            </div>
            <div class="text-orange-600">
                {{ $address->location }}
            </div>
            <x-address-phones :phonesCsv="$address->phones" />
            <div class="email text-orange-600">
                {{ $address->email }}
            </div>
        </div>

        <x-addressactions-card :address="$address" />

    </div>
</x-card>
