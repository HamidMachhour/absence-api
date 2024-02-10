<x-layout>

   <x-userinfo-card :user="$user" />
    @unless ($user_images->count() == 0)
        @auth
            @if ($user->id === auth()->user()->id || auth()->user()->hasRole("admin"))
                <div class="flex text-xs  text-white p-1">
                    <a href="{{ route('userImages.create', ['user' => $user->id]) }}"
                        class="block bg-orange-600 rounded-xl hover:opacity-80 p-1">
                        <i class="fa-solid fa-circle-plus"></i> اضافة صورة
                    </a>
                </div>
                <div class="lg:grid lg:grid-cols-2 gap-2 mt-2">
                    @foreach ($user_images as $user_image)
                        <div class="flex flex-col position-relative">
                            <img class="rounded-xl w-full" src="{{ $user_image->uri ?? asset('/images/no-image.png') }}" alt="" />
                            <div class="flex flex-col position-absolute gap-2 p-1 right-0 text-xs">
                                <form action="{{ route('userImages.destroy', ['image' => $user_image->id]) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class=" bg-red-600 text-white p-2 rounded-xl hover:opacity-80"
                                        onclick="return confirm('حدف الصورة؟')">
                                        <i class="fa-solid fa-cancel"></i>
                                        حدف
                                    </button>
                                </form>
                                @if ($user->imageId !== $user_image->id)
                                    <form
                                        action="{{ route('userImages.setDefault', ['user' => $user->id, 'image' => $user_image->id]) }}"
                                        method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class=" bg-gray-700 text-white p-2 rounded-xl hover:opacity-80"
                                            onclick="return confirm('Set image as default for current user?')">
                                            <i class="fa-solid fa-check"></i>
                                            اختر صورة رئيسية
                                        </button>
                                    </form>
                                @else
                                    <div class="flex">
                                        <div class="block text-gray-700 border-2 border-gray-700 rounded-xl p-2 text-xs">
                                            <i class="fa-solid fa-check"></i>
                                            صورة رئيسية
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="lg:grid lg:grid-cols-2 gap-2 mt-2">
                    @foreach ($user_images as $user_image)
                        <img class="rounded-xl w-full" src="{{ $user_image->uri ?? asset('/images/no-image.png') }}" alt="" />
                    @endforeach
                </div>
            @endif
        @else
            <div class="lg:grid lg:grid-cols-2 gap-2 mt-2">
                @foreach ($user_images as $user_image)
                    <img class="rounded-xl w-full" src="{{ $user_image->uri ?? asset('/images/no-image.png') }}" alt="" />
                @endforeach
            </div>
        @endauth
        <div class="mt-6 p-4">
            {{ $user_images->appends(request()->query->all())->links() }}
        </div>
    @else
        @if ($user->id === auth()->user()->id || auth()->user()->hasRole("admin"))
            <div class="flex text-xs  text-white p-1">
                <a href="{{ route('userImages.create', ['user' => $user->id]) }}"
                    class="block bg-orange-600 rounded-xl hover:opacity-80 p-1">
                    <i class="fa-solid fa-circle-plus"></i> اضافة صورة
                </a>
            </div>
        @endif
        <p>No images found</p>
    @endunless
</x-layout>
