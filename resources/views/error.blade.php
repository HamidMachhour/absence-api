<x-layout>


    {{-- @include('partials._search') --}}


    <x-card>
        <div class="flex gap-2 flex-wrap items-center">
        @if (isset($message))
            <p>
                {{ $message }}
            </p>
        @else
            <p>غير متوفر</p>
        @endif
        <div class="bg-gray-200 text-orange-600 rounded p-2 hover:bg-green-700">
            <a href="javascript:history.back()"> رجوع </a>
        </div>
        </div>
    </x-card>
</x-layout>
