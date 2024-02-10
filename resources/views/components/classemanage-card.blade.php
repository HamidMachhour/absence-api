@props(['classe'])

<x-card>
    @auth
        <div class="flex flex-row flex-wrap gap-2 justify-stretch text-xs">
            {{-- Check if the authenticated user's ID matches the store's user ID --}}
            @if (auth()->user()->hasRole("admin") || $classe->userId === auth()->user()->id)
                <div class="flex justify-center">
                    <a href="{{ route('classes.edit', ['classe' => $classe->id]) }}"
                        class="block bg-blue-500 text-white p-1 rounded-xl hover:opacity-80">
                        <i class="fa-solid fa-edit"></i>
                        تعديل القسم
                    </a>
                </div>
                <div class="flex justify-center">
                    <form action="{{ route('classes.destroy', ['classe' => $classe->id]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 text-white p-1 rounded-xl hover:opacity-80"
                            onclick="return confirm('Are you sure you want to delete this store?')">
                            <i class="fa-solid fa-trash"></i>
                            حدف القسم
                        </button>
                    </form>
                </div>
                <div class="flex justify-center">
                    <a href="{{ route('classes.importForm', ['classe' => $classe->id]) }}"
                        class="block bg-green-500 text-white p-1 rounded-xl hover:opacity-80">
                        <i class="fa-solid fa-arrow-down"></i>
                        تحميل
                    </a>
                </div>
                <div class="flex justify-center">
                    <a href="{{ route('classes.export', ['classe' => $classe->id]) }}"
                        class="block bg-gray-500 text-white p-1 rounded-xl hover:opacity-80">
                        <i class="fa-solid fa-arrow-up"></i>
                        تصدير
                    </a>
                </div>
            @endif
        </div>
    @endauth
</x-card>
