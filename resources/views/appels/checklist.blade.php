<x-layout>

    <x-card class="flex justify-center text-3xl">لائحة التلاميد</x-card>


    @unless (count($students) == 0)

        <x-card class="flex justify-center text-ms">
            <form method="POST" action="{{ route('appels.pushAllStudents') }}">
                @csrf
                <input type="hidden" name="seanceId" value="{{ $seance->id }}">

                <button type="submit" class="block  bg-green-700 text-white mb-2 p-2 rounded-xl hover:opacity-80"
                    onclick="return confirm('تأكيد تسجيل غياب جماعي؟')" >
                <i class="fa-solid fa-hand">غياب
                    جماعي</i>
                </button>
            </form>
        </x-card>

        <div class="lg:grid lg:grid-cols-4 gap-1 text-ms mb-2">

            @foreach ($students as $student)
                @if ($student->user && $student->classe)
                    <x-studentappel-card :student="$student" :seance="$seance" />
                @endif
            @endforeach

            <div class="mt-6 p-4">
                {{ $students->appends(request()->query->all())->links() }}
            </div>
        </div>
    @else
        <p>غير متوفر</p>
    @endunless

</x-layout>
