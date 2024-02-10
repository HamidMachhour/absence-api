@props(['tagsCsv'])

@php
    $tags = explode(',', $tagsCsv);
@endphp

<ul class="flex flex-wrap items-center text-gray-600 text-xs">
    @foreach ($tags as $tag)
        <li
            class="bg-gray-200 hover:opacity-80 rounded-xl p-1 m-1">
            <a href="{{request()->fullUrlWithQuery(['tag' => $tag,'page'=>1]) }}">{{ '#'.$tag }}</a>
        </li>
    @endforeach
</ul>
