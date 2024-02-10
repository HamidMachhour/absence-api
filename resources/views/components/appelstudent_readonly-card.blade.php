@props(['appel_student'])

<x-card>

    <div class="lg:grid lg:grid-cols-9 gap-1 text-orange-600">

        <a href="{{ route('students.show', ['student' => $appel_student->studentId]) }}" class="flex items-center border-1 border-orange-200 text-green-600 text-xs col-span-5">

            <div class="flex justify-center p-1">
                <img class="w-5 rounded-circle" src="{{ $appel_student->student->studentImage->uri ?? asset('/images/no-image.png') }}"
                alt="" />
            </div>
            <div class="flex justify-center p-1">

                {{ $appel_student->student->title }}

            </div>
        </a>
        <div class="flex items-center justify-center border-1 border-orange-200 text-xs p-1 ">
            {{ $price = $appel_student->student->price }}
        </div>
        <div class="flex  items-center justify-center border-1 border-orange-200 text-xs p-1 ">
            {{ $appel_student->plage }}
        </div>
        <div class="flex items-center justify-center border-1 border-orange-200 text-xs p-1">
            {{ $appel_student->discount }}
        </div>
        <div class="flex items-center justify-center border-1 border-orange-200 text-xs p-1">
            {{ $appel_student->total() }}
        </div>

    </div>



</x-card>
