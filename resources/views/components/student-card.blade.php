@props(['student'])

<div class="card card-primary card-outline collapsed-card m-1">
    <div class="card-header">
        <div class="card-title h3">
            <a href="{{ request()->fullUrlWithQuery(['studentId' => $student->id, 'page' => 1]) }}" class="flex text-blue-600 hover:text-gray-700 justify-center">
                {{ $fullname = $student->num . '-' . $student->user->fullName()  }}
            </a>
        </div>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body p-1">
        <div class="d-flex flex-column ">
            <div class="d-flex flex-column">
                <a href="{{ route('users.show', ['user' => $student->userId]) }}" class="flex justify-center">
                    <img src="{{ $student->user->userImage->uri ?? asset('/assets/images/no-image.png') }}" alt="Photo 1"
                        class="img-fluid" style="max-width: 150px;">
                </a>
                <div class="btn-group">
                    <div class="btn btn-default">
                        <i class="fas fa-person-running"></i>
                        <span class="badge bg-danger">{{ $student->nb_absence }}</span>
                    </div>
                    <div class="btn btn-default">
                        <i class="fas fa-clock"></i>
                        <span class="badge bg-purple">{{ round(($student->absences_sum_plage ?? 0) / 60, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="btn-group-vertical">
                <div class="btn btn-flat">
                    <i class="far fa-envelope"></i>
                    {{ $student->user->email }}
                </div>
                <div class="btn btn-flat">
                    <i class="far fa-id-badge"></i>
                    {{ $student->user->code }}
                </div>
            </div>
            <div class="user-panel d-flex">
                <div class="image">
                    <img src="{{ $student->classe->classeImage->uri ?? asset('/assets/images/no-image.png') }}"
                        class="img-circle elevation-2" alt="{{ $classename = $student->classe->classeName() }}">
                </div>
                <div class="info">
                    <a href="{{ request()->fullUrlWithQuery(['classeId' => $student->classeId, 'page' => 1]) }}"
                        class="d-block">{{ $classename }}</a>
                </div>
            </div>

            @auth
            @if (auth()->user()->id === $student->classe->userId || auth()->user()->hasRole('admin'))
            <x-studentactions-card :student="$student" />
            {{-- @else
                    <x-studentpublicactions-card :student="$student" /> --}}
            @endif
            @endauth
        </div>
    </div>
    <!-- /.card-body -->
</div>