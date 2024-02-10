@props(['params'])
@if (count($params) > 0)
    <div class="text-right mr-3">
        <div class="btn-group">
            <button type="button" class="btn btn-warning dropdown-toggle dropdown-icon" data-toggle="dropdown">
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu" role="menu">
                @foreach ($params as $param => $label)
                    @if (request()->has($param))
                        <a class="dropdown-item"
                            href="{{ request()->fullUrlWithoutQuery([$param]) }}">
                            <i class="fas fa-check"></i>
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
            <button type="button" class="btn btn-warning">معطيات مرشحة</button>

        </div>
    </div>
@endif
