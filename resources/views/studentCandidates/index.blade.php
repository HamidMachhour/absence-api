<x-layout>
    {{--   @if (!Auth::check())
    @include('partials._hero')
  @endif --}}

    {{-- @include('partials._students_search') --}}
    <x-card class="text-center text-3xl"> طلبات التسجيل</x-card>
    @unless ($students->count() == 0)
        @if (request()->filled('classeId'))
            <x-classeinf-card :classe="$students[0]->classe" />
        @endif
         <x-card> <div class="text-right">العدد الجمالي: {{$students->count()}}</div></x-card>
        <div class="lg:grid lg:grid-cols-4 gap-2 mb-2">

            @foreach ($students as $student)
                @if ($student->user && $student->classe)
                    <x-studentcandidate-card :student="$student" />
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
