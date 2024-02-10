@props(['seance'])

<x-card>
    <div class="grid gap-1 mb-2 text-xs">

        <div class="d-flex flex-column">
            <div>
                <a href="{{ request()->fullUrlWithQuery(['classeId' =>$seance->classeId, 'page' => 1]) }}"
                    class="flex text-green-600 hover:text-orange-700 justify-center">
                    {{ $seance->classe->name }}
                </a>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['matiereId' => $seance->matiereId, 'page' => 1]) }}"
                    class="flex text-orange-600 hover:text-green-700 justify-center">
                    {{ $seance->matiere->name }}
                </a>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['profId' => $seance->profId, 'page' => 1]) }}"
                    class="flex text-green-600 hover:text-orange-700 justify-center">
                    {{ $seance->professeur->firstName . ' ' . $seance->professeur->lastName }}
                </a>
            </div>
            <div class="flex gap-2 items-center text-orange-700 justify-end">
                <a href="{{ request()->fullUrlWithQuery(['jourId' => $seance->jourId, 'page' => 1]) }}"
                    class="flex text-orange-600 hover:text-green-700 justify-center">
                    {{ $seance->jour->name }}
                </a>
            </div>
            <div class="flex gap-2 items-center text-orange-700 justify-end">
                <div>{{ \Carbon\Carbon::parse($seance->de)->format('H:i') }}</div>
                ->
                <div>{{ \Carbon\Carbon::parse($seance->a)->format('H:i') }}</div>

            </div>
            <div class="flex gap-2 items-center text-orange-700 justify-end">
                <i class="fa-solid fa-home"></i>
                <div>{{ $seance->salle }}</div>
                <div>:القاعة</div>
            </div>

        </div>
        <x-seanceactions-card :seance="$seance" />
    </div>
</x-card>
