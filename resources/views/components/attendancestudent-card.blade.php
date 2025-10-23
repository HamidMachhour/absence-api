@props(['attendance_student','seance'])



<div class="card card-widget widget-user-2 shadow-sm mx-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header bg-info">
        <a href="{{ route('students.show', ['student' => $attendance_student->studentId]) }}" class="widget-user-image">
            <img class="img-circle elevation-1"
                alt="{{ $user_name = $attendance_student->student->num . '-' . $attendance_student->student->user->fullName() }}"
                src="{{ $attendance_student->student->studentImage->uri ?? asset('/assets/images/no-image.png') }}">
        </a>
        <!-- /.widget-user-image -->
        <h3 class="widget-user-username">{{ $user_name }}</h3>
        <h5 class="widget-user-desc">{{ $attendance_student->created_at }} </h5>
    </div>
    <div class="card-footer p-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <form
                    action="{{ route('attendances.savePlage', ['attendance_student' => $attendance_student]) }}"
                    method="POST">

                    <div class="input-group">
                        <div class="input-group-prepend">
                            @csrf
                            <button class="input-group-text bg-success" type="submit">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                        <input class="form-control" type="number" name="plage" min="1"
                            max="{{ $seance->plage() }}" step="1"
                            value="{{ $attendance_student->plage }}" >
                    </div>
                </form>
            </li>
            <li class="nav-item">
                <div class="input-group">
                    {{ $attendance_student->justification }}
                </div>
            </li>
            <li class="nav-item ">
                <div class="btn-group m-1">
                    <a href="{{ route('seances.show', ['seance' => $seance]) }}"
                        class="btn btn-app bg-warning">
                        <i class="fas fa-clock"></i>{{__('vocab.seance')}}
                    </a>
                    <form
                        action="{{ route('attendances.popAttendanceStudent', ['attendance_student' => $attendance_student]) }}"
                        method="POST" class="inline text-xs">
                        @csrf
                        <button type="submit" class="btn btn-app bg-danger" onclick="return confirm('{{__('vocab.confirm')}}')">
                            <i class="fas fa-trash"></i>
                            {{__('vocab.delete')}}
                        </button>
                    </form>
                </div>

            </li>
        </ul>
    </div>
</div>
