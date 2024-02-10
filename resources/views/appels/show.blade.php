<x-layout>


    <x-card class="flex flex-row flex-wrap justify-center bg-orange-200 gap-2 mb-2">
        <x-appel-card :appel="$appel" />
    </x-card>

    <div class="lg:grid lg:grid-cols-4 gap-1 ">

        @unless (count($appel->appelStudents) == 0)

            @foreach ($appel->appelStudents as $appel_student)
                <x-appelstudent-card :appel_student="$appel_student" />
            @endforeach
        @else
            <p class="flex justify-center">لا شيء</p>
        @endunless

    </div>

</x-layout>
