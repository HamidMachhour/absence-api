<x-layout>

    <x-card class="flex flex-row flex-wrap justify-center gap-2 mb-2">
        <x-classeinf-card :classe="$classe" />
        <x-classemanage-card :classe="$classe" />
    </x-card>
    <x-filters-card :params="$params" />

    <div class="lg:grid lg:grid-cols-2 gap-2 mt-2">

        @unless (count($students) == 0)

            @foreach ($students as $student)
                <x-student-card :student="$student" />
            @endforeach
        @else
            <p>غير متوفر</p>
        @endunless

    </div>

    <div class="mt-6 p-4">
        {{ $students->appends(request()->query->all())->links() }}
    </div>
</x-layout>
