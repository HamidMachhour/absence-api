<x-layout>

    <x-card class="p-10 max-w-lg mx-auto mt-24">
        <header class="text-center">
            <h2 class="text-2xl font-bold uppercase mb-1">Edit Address</h2>
        </header>

        <form method="POST" action="{{ route('addresses.update', ['address' => $address->id]) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="strAddress" class="inline-block text-lg mb-2">اسم الشارع، الحي </label>
                <input type="text" class="border border-gray-200 rounded p-2 w-full" name="strAddress"
                    placeholder="اسم الشارع، الحي  " value="{{ $address->strAddress }}" />

                @error('strAddress')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label for="city" class="inline-block text-lg mb-2">المدينة</label>
                <input type="text" class="border border-gray-200 rounded p-2 w-full" name="city"
                    placeholder="city" value="{{ $address->city }}" />

                @error('city')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label for="zipCode" class="inline-block text-lg mb-2">Address zipcode</label>
                <input type="number" step="1" class="border border-gray-200 rounded p-2 w-full"
                    name="zipCode" placeholder="zip code" value="{{ $address->zipCode }}" />

                @error('zipCode')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="country" class="inline-block text-lg mb-2">البلد</label>
                <input type="text" class="border border-gray-200 rounded p-2 w-full" name="country"
                    value="{{ $address->country }}" />
                @error('country')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="location" class="inline-block text-lg mb-2">الاجداثيات على الخريطة</label>
                <input type="text" class="border border-gray-200 rounded p-2 w-full" name="location"
                    value="{{ $address->location }}" />
                @error('location')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="phones" class="inline-block text-lg mb-2">
                    ارقام الهواتف (Comma Separated)
                </label>
                <input type="text" class="border border-gray-200 rounded p-2 w-full" name="phones"
                    value="{{ $address->phones }}" />

                @error('phones')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="email" class="inline-block text-lg mb-2">
                    البريد الالكتروني
                </label>
                <input type="email" class="border border-gray-200 rounded p-2 w-full" name="email" rows="10"
                    value="{{ $address->email }}" />

                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-cols gap-2">

                <button type="submit" class="block bg-orange-600 text-white rounded py-2 px-4 hover:bg-green-700">
                    <i class="fa-solid fa-save"></i>
                    حفظ
                </button>
                <div class="flex justify-center bg-gray-400 text-white rounded p-3 hover:bg-green-700 text-xs p-1">
                    <a href="javascript:history.back()" class=" block ">
                        <i class="fa-solid fa-circle-arrow-left"></i>
                        رجوع
                    </a>
                </div>

            </div>
        </form>
    </x-card>
</x-layout>
