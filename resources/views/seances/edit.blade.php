<x-layout>
    <x-card class="p-10 max-w-lg mx-auto">
        <header class="text-center">
            <h2 class="text-2xl font-bold uppercase mb-1">تعديل الحصة</h2>
        </header>

        <form method="POST" action="{{ route('seances.update', ['seance' => $seance->id]) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1">

                <div class="grid grid-cols-3 items-center   text-right">
                    @error('profId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <select id="profId" name="profId"
                        class="select col-span-1 border-1 border-gray-200 rounded-md text-orange-600 text-right">
                        <option value=""></option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $seance->profId == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->firstName . ' ' . $teacher->lastName }}</option>
                        @endforeach
                    </select>
                    <label for="profId" class="col-span-1 text-lg mb-2">الاستاد</label>

                </div>
                <div class="grid grid-cols-3 items-center   text-right">
                    @error('classeId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <select id="classeId" name="classeId"
                        class="select col-span-1 border-1 border-gray-200 rounded-md text-orange-600 text-right">
                        <option value=""></option>
                        @foreach ($classes as $classe)
                            <option value="{{ $classe->id }}"
                                {{ $seance->classeId == $classe->id ? 'selected' : '' }}>
                                {{ $classe->name }}</option>
                        @endforeach
                    </select>
                    <label for="classeId" class="col-span-1 text-lg mb-2">القسم</label>

                </div>
                <div class="grid grid-cols-3 items-center   text-right">
                    @error('matiereId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <select id="matiereId" name="matiereId"
                        class="select col-span-1 border-1 border-gray-200 rounded-md text-orange-600 text-right">
                        <option value=""></option>
                        @foreach ($matieres as $matiere)
                            <option value="{{ $matiere->id }}"
                                {{ $seance->matiereId == $matiere->id ? 'selected' : '' }}>
                                {{ $matiere->name }}</option>
                        @endforeach
                    </select>
                    <label for="matiereId" class="col-span-1 text-lg mb-2">المادة</label>

                </div>
                <div class="grid grid-cols-3 items-center   text-right">
                    @error('jourId')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <select id="jourId" name="jourId"
                        class="select col-span-1 border-1 border-gray-200 rounded-md text-orange-600 text-right">
                        <option value=""></option>
                        @foreach ($jours as $jour)
                            <option value="{{ $jour->id }}" {{ $seance->jourId == $jour->id ? 'selected' : '' }}>
                                {{ $jour->name }}</option>
                        @endforeach
                    </select>
                    <label for="jourId" class="col-span-1 text-lg mb-2">اليوم</label>

                </div>

                <div class="grid grid-cols-3 items-center   text-right">
                    @error('de')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <select id="de" name="de"
                        class="select col-span-1 border-1 border-gray-200 rounded-md text-orange-600 text-right">
                        <option value=""></option>
                        @foreach ($hours as $hour)
                            <option value="{{ $hour }}"
                                {{ \Carbon\Carbon::parse($seance->de)->format('H:i') === $hour ? 'selected' : '' }}>
                                {{ $hour }}</option>
                        @endforeach
                    </select>
                    <label for="de" class="col-span-1 text-lg mb-2">من</label>

                </div>

                <div class="grid grid-cols-3 items-center   text-right">
                    @error('a')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <select id="a" name="a"
                        class="select col-span-1 border-1 border-gray-200 rounded-md text-orange-600 text-right">
                        <option value=""></option>
                        @foreach ($hours as $hour)
                            <option value="{{ $hour }}"
                                {{ \Carbon\Carbon::parse($seance->a)->format('H:i') === $hour ? 'selected' : '' }}>
                                {{ $hour }}</option>
                        @endforeach
                    </select>
                    <label for="a" class="col-span-1 text-lg mb-2">الى</label>

                </div>

                <div class="grid grid-cols-3 items-center text-right">
                    @error('salle')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <input type="number" class="border text-right border-gray-200 rounded p-2 w-full" name="salle"
                        value="{{ $seance->salle }}" />

                    <label for="salle" class="col-span-1 text-lg mb-2">
                        القاعة
                    </label>
                </div>

                <div class="grid grid-cols-3 items-center gap-2 mt-2">
                    <button class="bg-orange-600 text-white rounded py-2 px-4 hover:bg-green-700">
                        حفظ
                    </button>

                    <a href="javascript:history.back()"
                        class="bg-gray-200 text-center text-orange-600 rounded py-2 px-4 hover:bg-green-700">
                        رجوع </a>
                </div>
            </div>
        </form>
    </x-card>
</x-layout>
