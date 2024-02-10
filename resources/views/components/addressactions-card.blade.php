@props(['address'])

<x-card class="d-flex flex-column justify-stretch text-xs">
    @auth
        @if ($address->userId === auth()->user()->id)
            <a href="{{ route('addresses.edit', ['address' => $address->id]) }}"
                class="flex justify-center bg-blue-500 text-white mb-2 p-2 rounded-xl hover:opacity-80">
                <div classs="block">
                    <i class="fa-solid fa-edit"></i>
                    تعديل
                </div>
            </a>
            @if ($address->id !== auth()->user()->addressId)
                <form action="{{ route('addresses.default', ['address' => $address->id]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full bg-gray-700 text-white mb-2 p-2 rounded-xl hover:opacity-80"
                        onclick="return confirm('Are you sure you want to set current address as default?')">
                        <i class="fa-solid fa-check"></i>
                        اختر عنوان رئيسي </button>
                </form>
            @else
                <div class="flex">
                    <div class="block text-gray-700 border-2 border-gray-700 rounded-xl p-1 m-2 text-xs">
                        <i class="fa-solid fa-check"></i>
                         عنوان رئيسي
                    </div>
                </div>
            @endif
            <form action="{{ route('addresses.destroy', ['address' => $address->id]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-500 text-white  mb-2 p-2 rounded-xl hover:opacity-80"
                    onclick="return confirm('Are you sure you want to delete this address?')">
                    <i class="fa-solid fa-trash"></i>
                    حدف
                </button>
            </form>
        @endif

    @endauth

</x-card>
