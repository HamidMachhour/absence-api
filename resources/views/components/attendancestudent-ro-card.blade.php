@props(['attendance_student'])

<div class="card card-widget widget-user-2 shadow-sm mx-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header">
        <a href="{{route('users.show',['user'=>$attendance_student->student->user])}}">
        <img class="img-circle elevation-1 w-20"
            alt="{{ $user_name = $attendance_student->student->num . '-' . $attendance_student->student->user->fullName() }}"
            src="{{ $attendance_student->student->studentImage->uri ?? asset('/assets/images/no-image.png') }}">
        <!-- /.widget-user-image -->
        <h3 class="widget-user-username">{{ $user_name }}</h3>
        </a>
        <h5 class="widget-user-desc">{{ $attendance_student->created_at }} </h5>
    </div>
    <div class="card-footer p-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <div class="input-group">
                    {{ $attendance_student->justification }}
                </div>
            </li>
        </ul>
    </div>
</div>