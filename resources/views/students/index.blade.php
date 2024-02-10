<x-layout>
    {{--   @if (!Auth::check())
    @include('partials._hero')
  @endif --}}

    {{-- @include('partials._students_search') --}}
    <x-card class="flex justify-center text-3xl">لائحة التلاميد</x-card>
    <x-filters-card :params="$params" />

    @unless (count($students) == 0)

        @if (request()->filled('classeId'))
            <x-classeinf-card :classe="$students[0]->classe" />
        @endif

        <div class="lg:grid lg:grid-cols-3 gap-1 text-ms mb-2">

            @foreach ($students as $student)
                @if ($student->user && $student->classe)
                    <x-student-card :student="$student" />
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
