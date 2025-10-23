@props(['address'])

<div class='card card-primary card-outline'>

    <div class="card-body">

        <h5 class="card-title">
            <a href="{{ route('addresses.show', ['address' => $address->id]) }}">
                {{ $address->strAddress }}
            </a>
        </h5>
        <div class="card-text ">
            {{ $address->city }}
        </div>
        <div class="card-text ">
            {{ $address->zipCode }}
        </div>
        <div class="card-text ">
            {{ $address->country }}
        </div>
        <div class="card-text ">
            {{ $address->location }}
        </div>
        <x-address-phones :phonesCsv="$address->phones" />
        <div class="card-text ">
            {{ $address->email }}
        </div>

        <x-addressactions-card :address="$address" />

    </div>
</div>
