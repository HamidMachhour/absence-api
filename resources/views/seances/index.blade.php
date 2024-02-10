<x-layout>
    {{--   @if (!Auth::check())
    @include('partials._hero')
  @endif --}}
    {{-- @include('partials._search') --}}
    <x-card class="flex justify-center text-3xl mb-2">
        جدول الحصص
    </x-card>
    <x-filters-card :params="$params" />

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
