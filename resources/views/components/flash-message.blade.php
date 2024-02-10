@if(session()->has('message'))
<div x-data="{show: true}" x-init="setTimeout(() => show = false, 2000)" x-show="show"
  class="fixed bottom-0 left-1/2 transform -translate-x-1/2 bg-orange-600 text-white px-48 py-3">
  <p>
    {{session('message')}}
  </p>
</div>
@endif
