<x-layout>

    <x-card class="lg:grid lg:grid-cols-9 gap-1 text-white text-xs ">
        <div class="flex justify-center w-full bg-orange-600   p-1">
            Absence Id
        </div>

        <div class="flex justify-center w-full bg-orange-600   p-1">
            Created At
        </div>
        <div class="flex justify-center w-full bg-orange-600   p-1">
            Updated At
        </div>
        <div class="flex justify-center w-full bg-orange-600   p-1">
            NB Items
        </div>
        <div class="flex justify-center w-full bg-orange-600   p-1">
            Price
        </div>
        <div class="flex justify-center w-full bg-orange-600   p-1">
            Latest Status
        </div>
        <div class="flex justify-center w-full bg-orange-600 col-span-3  p-1">
            Actions
        </div>
    </x-card>
    @foreach ($absences as $absence)
        <x-absence-card :absence="$absence" />
    @endforeach


</x-layout>
