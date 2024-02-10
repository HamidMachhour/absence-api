<x-layout>

    <x-card class="flex justify-center text-3xl mb-2">جدول الحصص</x-card>
   <x-filters-card :params="$params" />
    @auth
        @if (auth()->user()->hasRole('admin'))
            <x-card>
                <a href="{{ route('seances.create') }}"
                    class="text-right block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                    حصة جديدة<span>
                        <i class="fa-solid fa-plus"></i>
                </a>
            </x-card>
        @endif
    @endauth


    <div class="lg:grid lg:grid-cols-4 gap-2 mt-2">

        @unless (count($seances) == 0)

            @foreach ($seances as $seance)
                <x-seance-card :seance="$seance" />
            @endforeach
        @else
            <p>لا شيء</p>
        @endunless

    </div>

    <div class="mt-6 p-4">
        {{ $seances->appends(request()->query->all())->links() }}
    </div>
</x-layout>
