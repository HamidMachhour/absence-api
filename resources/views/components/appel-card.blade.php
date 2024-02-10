@props(['appel'])

<x-card class="lg:grid lg:grid-cols-2 gap-1 text-green-600">
    <div class="lg:grid lg:grid-cols-2 gap-1 text-white col-span-2">
        <div class="flex justify-center border-1 border-orange-600 text-orange-600 text-md p-1">
            {{ count($appel->appelStudents) }}
        </div>
        <div class="flex items-center justify-center  bg-orange-600 text-xs p-1">
            الغائبون
        </div>
    </div>

    @if (count($appel->appelStudents) > 0)
        <div
            class="flex items-center justify-center w-full bg-red-500 text-white p-1 rounded-xl hover:opacity-80 text-xs p-1">
            <form action="{{ route('appels.clear', ['appel' => $appel->id]) }}" method="POST" class="flex text-xs">
                @csrf
                <button type="submit" class="block w-full  text-white p-1 rounded-xl hover:opacity-80"
                    onclick="return confirm('تأكيد الحدف؟ ?')">
                    <i class="fa-solid fa-x"></i>
                    حدف الكل
                </button>
            </form>
        </div>
        <div
            class="flex items-center justify-center w-full bg-green-600 text-white p-1 rounded-xl hover:opacity-80 text-xs p-1">
            <a href="{{ route('appels.appel_to_absence', ['appel' => $appel->id]) }}" class=" block ">
                <i class="fa-solid fa-circle-arrow-right"></i>
                تأكيد الغياب
            </a>
        </div>
    @endif
</x-card>
