@props(['phonesCsv'])

@php
    $phones = explode(',', $phonesCsv);
@endphp

<ul class="flex flex-wrap items-center text-gray-600 text-xs">
    @foreach ($phones as $phone)
        <li
            class=" bg-gray-200 hover:opacity-80 rounded-xl p-1 m-1">
            <a href="{{ request()->fullUrlWithQuery(['phone' => $phone,'page'=>1]) }}">{{ $phone }}</a>
        </li>
    @endforeach
</ul>
