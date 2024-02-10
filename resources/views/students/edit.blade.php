<x-layout>

    <x-card class="p-10 max-w-lg mx-auto mt-24">
        <header class="text-center">
            <h2 class="text-2xl font-bold mb-1">تغيير المعطيات</h2>
            @if (Auth::user()->hasRole('admin') || Auth::user()->id === $student->user->id)
            <a href="{{ route('users.edit', ['user' => $student->user]) }}" class="hover:text-green-700">
                <p class="mb-4">{{ $student->user->firstName }}<span> {{ $student->user->lastName }}</p>
            </a>
            @else
            <p class="mb-4">{{ $student->user->firstName }}<span> {{ $student->user->lastName }}</p>
            @endif
        </header>

        <form method="POST" action="{{ route('students.update', ['student' => $student->id]) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-6 text-right">
                <label for="num" class="inline-block text-lg mb-2">رقم التلميد</label>
                <input type="text" class="border border-gray-200 rounded text-orange-600 p-2 w-full text-right"
                    name="num" id="num" placeholder="Example: 1,2,3,...,20" value="{{ $student->num }}" />

                @error('num')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center  justify-center text-right col-span-2">

                <select id="classeId" name="classeId"
                    class="select border-1 border-gray-200 rounded-md text-orange-600 text-right">
                    @foreach ($classes as $classe)
                        <option value="{{ $classe->id }}" {{ $classe->id === $student->classeId ? 'selected' : '' }}>
                            {{ $classe->name }}</option>
                    @endforeach
                </select>
                <label for="classeId" class="inline-block text-lg mb-2">القسم</label>
                @error('classeId')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6 text-right">
                <a href="{{ route('students.index') }}?classeId={{ $student->classeId }}"
                    class="text-orange-600 hover:text-green-700"> رجوع </a>

                <button class="bg-orange-600 text-white rounded py-2 px-4 hover:bg-green-700">
                    حفظ
                </button>

            </div>
        </form>
    </x-card>
</x-layout>
