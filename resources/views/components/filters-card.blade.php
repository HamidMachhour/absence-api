@props(['params'])
@if (count($params) > 0)
    <div class="btn-group ml-2">
        <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu" role="menu">
            @foreach ($params as $param => $label)
              @if (request()->has($param))
                    <a class="dropdown-item" href="{{ request()->fullUrlWithoutQuery([$param]) }}">
                        <i class="fas fa-x"></i>
                        {{ $label }}
                    </a>
                @endif
            @endforeach
        </div>
    </div>
@endif
