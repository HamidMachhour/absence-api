@props(['attendance'])

<!-- small card -->
<div class="small-box bg-warning mx-2">
    <div class="inner">
        <h3>{{ count($attendance->attendanceStudents) }}</h3>
        <p> {{__('vocab.absence_list')}} </p>
    </div>
    <div class="icon">
        <i class="fas fa-person-running"></i>
    </div>
    @if (count($attendance->attendanceStudents) > 0)
        <div class="small-box-footer">
            <div class="btn-group">

                <a href="{{ route('attendances.qrcodeCheck', ['attendance' => $attendance->id]) }}" class="btn btn-app mx-1 bg-info">
                    <i class="fas fa-share"></i>
                    {{__('vocab.confirm')}} {{__('vocab.present')}}
                </a>
                <form action="{{ route('attendances.clear', ['attendance' => $attendance]) }}" method="GET">
                    @csrf
                    <button type="submit" class="btn btn-app mx-1 bg-danger"
                        onclick="return confirm('{{__('vocab.confirm')}}')">
                        <i class="fas fa-trash"></i>
                        {{__('vocab.delete_all')}}
                    </button>
                </form>
                <a href="{{ route('attendances.attendance_to_absence', ['attendance' => $attendance->id]) }}"
                    class="btn btn-app mx-1 bg-success">
                    <i class="fas fa-circle-arrow-right"></i>
                    {{__('vocab.confirm')}}
                                </a>
            </div>
        </div>
    @endif
</div>
