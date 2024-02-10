<x-layout>
    {{--   @if (!Auth::check())
    @include('partials._hero')
  @endif --}}
    {{-- @include('partials._search') --}}

    <x-card class="flex mb-2 gap-2">
        <div class="flex justify-left text-lg text-orange-600 ">
            عناويني
        </div>
        <div class="flex justify-center text-xs bg-orange-600 text-white p-1 rounded-xl hover:opacity-80">
            <a href="{{ route('addresses.create') }}" class="block ">
                <i class="fa-solid fa-circle-plus"></i> عنوان جديد
            </a>
        </div>
    </x-card>
    <div class="lg:grid lg:grid-cols-2 gap-2 mb-2">

        @unless (count($addresses) == 0)

            @foreach ($addresses as $address)
                <x-address-card :address="$address" />
            @endforeach
        @else
            <p>لا شيء</p>
        @endunless
    </div>

    <div class="mt-6 p-4">
        {{ $addresses->appends(request()->query->all())->links() }}
    </div>
</x-layout>
