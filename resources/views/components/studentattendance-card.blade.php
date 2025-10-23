@props(['student', 'attendance'])


<div class="card card-widget widget-user-2 shadow-sm mx-2">
    <!-- Add the bg color to the header using any of the bg-* classes -->
    <div class="widget-user-header {{ $student->hasCut() ? 'bg-orange' : 'bg-info' }}">
        <a href="{{ route('students.show', ['student' => $student]) }}" class="widget-user-image">
            <img class="img-circle elevation-2"
                alt="{{ $user_name = $student->num . '-' . $student->user->fullName()  }}"
                src="{{ $student->studentImage->uri ?? asset('/assets/images/no-image.png') }}">
        </a>
        <!-- /.widget-user-image -->
        <h5 class="widget-user-username">{{ $user_name }}</h5>
    </div>
    <div class="card-footer p-0">
        <ul class="nav flex-column">
            <li class="nav-item ">
                <div class="btn-group m-1">

                            <form method="POST" action="{{ route('attendances.pushStudent',['attendance'=>$attendance]) }}">
                                @csrf
                                <input type="hidden" name="studentId" value="{{ $student->id }}">
                                <button type="submit" class="btn btn-app bg-warning">
                                    <i class="fas fa-hand"></i>
                                    {{__('vocab.absent')}}
                                </button>
                            </form>
                </div>

            </li>
        </ul>
    </div>
</div>
