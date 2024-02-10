@props(['appel_student'])

<x-card>

    <div class="d-flex flex-wrap text-orange-600">

        <a href="{{ route('students.show', ['student' => $appel_student->studentId]) }}"
            class="flex items-center justify-end border-1 border-orange-200 text-green-600 text-xs ">
            <div class="flex justify-end p-1">

                {{ $appel_student->student->num }}
                <span> - <span>
                        {{ $appel_student->student->user->firstName }}
                        <span>
                            {{ $appel_student->student->user->lastName }}
            </div>
            <div class="flex  justify-end p-1">
                <img class="w-10 rounded-circle"
                    src="{{ $appel_student->student->studentImage->uri ?? asset('/images/no-image.png') }}"
                    alt="" />
            </div>

        </a>
        <div class="flex flex-wrap justify-end">
            <a href="{{ route('seances.show', ['seance' => $appel_student->seanceId]) }}"
                class="bg-gray-400 rounded-xl  hover:opacity-80 p-1">
                الحصة
            </a>
        </div>
        <form class="flex items-center justify-end text-xs  border-1 border-orange-200 gap-2 p-1 "
            action="{{ route('appels.savePlage', ['appel' => $appel_student->appelId, 'appel_student' => $appel_student->id]) }}"
            method="POST">
            <div class="flex justify-end text-xs ">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white p-1 rounded-xl hover:opacity-80">
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>
            <div class="flex items-center justify-end text-xs">
                <input class="input number w-full  text-center " type="number" name="plage" min="1"
                    max="{{ $appel_student->seance->plage() }}" step="1" value="{{ $appel_student->plage }}"
                    placeholder="المدة الزمنية" />
            </div>
        </form>
        <form class="flex items-center justify-end text-xs  border-1 border-orange-200 gap-2 p-1 "
            action="{{ route('appels.saveJustification', ['appel' => $appel_student->appelId, 'appel_student' => $appel_student->id]) }}"
            method="POST">
            <div class="flex justify-end text-xs ">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white p-1 rounded-xl hover:opacity-80">
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>
            <div class="flex items-center justify-end text-xs ">
                <input class="input  w-full  text-center " type="text" name="justification"
                    value="{{ $appel_student->justification }}" placeholder="تبرير الغياب" />
            </div>
        </form>
        <div class="flex items-center justify-end text-xs  border-1 border-orange-200 gap-2 p-1 ">
            {{ $appel_student->created_at }} :سجل بتاريخ
        </div>
        <div class="flex items-center justify-end   text-white p-1  p-1 ">

            <form
                action="{{ route('appels.popStudent', ['appel' => $appel_student->appelId, 'appel_student' => $appel_student->id]) }}"
                method="POST" class="inline text-xs">
                @csrf
                <button type="submit" class="block bg-red-500 rounded-xl hover:opacity-80 p-2"
                    onclick="return confirm('تأكيد الحدف؟')">
                    <i class="fa-solid fa-x"></i>
                    حدف
                </button>
            </form>
        </div>
    </div>



</x-card>
