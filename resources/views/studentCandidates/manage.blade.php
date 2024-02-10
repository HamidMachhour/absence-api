<x-layout>

    <x-card class="text-center text-3xl"> طلبات التسجيل بالقسم</x-card>

    <x-card class="flex flex-row flex-wrap justify-center bg-orange-200 gap-2 mb-2">
        <x-classeinf-card :classe="$classe" />
    </x-card>

    {{--     @include('partials._students_search')
 --}}
 <x-card> <div class="text-right">العدد الجمالي: {{$students->count()}}</div></x-card>

    <div class="lg:grid lg:grid-cols-4 gap-2 mb-2">

        @unless (count($students) == 0)

            @foreach ($students as $student)
                @if ($student->user && $student->classe)
                    <x-studentcandidate-card :student="$student" />
                @endif
            @endforeach
        @else
            <p>No students found</p>
        @endunless

    </div>

    <div class="mt-6 p-4">
        {{ $students->appends(request()->query->all())->links() }}
    </div>
</x-layout>
