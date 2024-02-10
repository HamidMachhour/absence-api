@props(['seance'])
@auth
    <x-card class="flex flex-wrap gap-2 justify-end text-xs ">

        @if (auth()->user()->id === $seance->profId)
            <a href="{{route('seances.checklist',['seance'=>$seance])}}" class="flex justify-center bg-green-600 text-white mb-2 p-2 rounded-xl hover:opacity-80">
                <div class="block">
                    <i class="fa-solid fa-check"></i>
                    تسجيل الغياب
                </div>
            </a>
        @elseif (auth()->user()->hasRole('admin') ||
                auth()->user()->hasRole('manager'))
            <form action="{{ route('seances.destroy', ['seance' => $seance->id]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full bg-red-500 text-white  mb-2 p-2 rounded-xl hover:opacity-80"
                    onclick="return confirm('انت على وشك الحدف، هل تود الاستمرار؟')">
                    <i class="fa-solid fa-trash"></i>
                    حدف
                </button>
            </form>
            <a href="{{ route('seances.edit', ['seance' => $seance->id]) }}" class="flex justify-center bg-blue-600 text-white mb-2  p-2 rounded-xl hover:opacity-80">
                <div classs="block">
                    <i class="fa-solid fa-edit"></i>
                    تعديل
                </div>
            </a>
        @endif
    </x-card>

@endauth
