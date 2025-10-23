@props(['absenceStudent'])



<div class="card card-widget widget-user-2 shadow-sm mx-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-info">
        <a href="{{ route('students.show', ['student' => $absenceStudent->studentId]) }}" class="widget-user-image">
            <img class="img-circle elevation-1"
                alt="{{ $user_name = $absenceStudent->student->num . '-' . $absenceStudent->student->user->fullName()  }}"
                src="{{ $absenceStudent->student->studentImage->uri ?? asset('/assets/images/no-image.png') }}">
        </a>
        <!-- /.widget-user-image -->
        <h3 class="widget-user-username">{{ $user_name }}</h3>
        <h5 class="widget-user-desc">{{ $absenceStudent->created_at->format('Y-m-d H:i') }} </h5>
    </div>
    <div class="card-footer p-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <div class="input-group">
                    {{ $absenceStudent->plage }} min
                </div>
            </li>
            @auth
                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <li class="nav-item">
                        <form
                            action="{{ route('absences.saveJustification', ['absence' => $absenceStudent->absenceId, 'absence_student' => $absenceStudent->id]) }}"
                            method="POST">

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    @csrf
                                    <button class="input-group-text bg-success" type="submit">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                                <input class="form-control" type="text" name="justification"
                                    value="{{ $absenceStudent->justification }}" placeholder="{{ __('vocab.absence_justify') }}">
                            </div>
                        </form>
                    </li>
                    <li class="nav-item ">
                        <div class="btn-group m-1">
                            <form
                                action="{{ route('absences.popStudent', ['absence' => $absenceStudent->absenceId, 'absence_student' => $absenceStudent->id]) }}"
                                method="POST" class="inline text-xs">
                                @csrf
                                <button type="submit" class="btn bg-danger" onclick="return confirm('{{ __('vocab.confirm') }}');">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>

                    </li>
                @endif
            @endauth

        </ul>
    </div>
</div>
