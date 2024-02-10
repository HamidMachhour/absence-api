<x-layout>
    <x-card class="p-10 max-w-lg mx-auto mt-24">
        <header class="text-center">
            <h2 class="text-2xl font-bold uppercase mb-1">اضافة صورة</h2>
        </header>

        <form method="POST" action="{{ route('userImages.store') }}">
            @csrf
            <div class="mb-6">
                <label for="uri" class="inline-block text-lg mb-2">رابط الصورة</label>
                <input type="text" class="border border-gray-200 rounded p-2 w-full" name="uri"
                    placeholder="http://..." value="{{ old('uri') }}" />

                @error('uri')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <input type="hidden" name="userId" value="{{ $user->id }}">

            <div class="mb-6">
                <button class="bg-orange-600 text-white rounded py-2 px-4 hover:bg-green-700">
                    حفظ
                </button>

                <a href="javascript:history.back()" class="bg-gray-200 text-orange-600 rounded p-3 hover:bg-green-700">
                    رجوع </a>
            </div>
        </form>
    </x-card>
</x-layout>
