<x-layout>
{{--     @if (!Auth::check())
        @include('partials._hero')
    @endif --}}
    <x-card class="flex flex-row flex-wrap justify-center bg-orange-500 gap-2 mb-2">
    <x-classeinf-card :classe="$classe" />
    </x-card>

    <x-card class="p-10 max-w-lg mx-auto">
        <header class="text-center">
          <h2 class="text-2xl font-bold uppercase mb-1">Import students</h2>
        </header>

        <form method="POST" action="{{ route('classes.import', ['classe' => $classe->id]) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-6">
            <input type="file" name="file" class="form-control">
            <button type="submit" class="bg-orange-600 text-white rounded py-2 px-4 hover:bg-green-700 mt-2">
                Import
              </button>

            @error('file')
            <p class="text-red-500 text-xs mt-1">{{$message}}</p>
            @enderror
          </div>

        </form>
      </x-card>
</x-layout>
