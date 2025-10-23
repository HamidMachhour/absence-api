@props(['enrollment'])
<tr>
    <td>
        {{ $enrollment->created_at }}
    </td>
    <td>
        <div class="user-panel">
            <a href="{{ route('userImages.show', ['user' => $enrollment->userId]) }}" class="image">
                <img src="{{ $enrollment->user->userImage->uri ?? asset('/assets/images/no-image.png') }}" class="brand-image img-circle elevation-1">
            </a>
            <div class="info">
                <a href="{{ route('users.show', ['user' => $enrollment->userId]) }}" class="d-block">
                    {{ $enrollment->user->fullName() }}
                </a>
            </div>
        </div>
    </td>
    <td>
        <div class="user-panel">
            <a class="image"  href="{{ route('enrollments.index', ['classe' => $enrollment->classeId]) }}">
                <img class="brand-image img-circle elevation-1"
                    src="{{ $enrollment->classe->classeImage->uri ?? asset('/assets/images/no-image.png') }}" alt="" />
            </a>
            <div class="info">
                {{ $enrollment->classe->classeName() }}
            </div>
        </div>
    </td>
    <td>
        @auth
            @if (auth()->user()->id === $enrollment->classe->userId || auth()->user()->hasRole('admin'))
                <x-enrollmentactions-card :enrollment="$enrollment" />
            @endif
        @endauth
    </td>
</tr>
