   @unless (count($attendance->attendanceStudents) == 0)
    <div class="lg:grid lg:grid-cols-4 mt-1">
        @foreach ($attendance->attendanceStudents as $attendance_student)
        <x-attendancestudent-ro-card :attendance_student="$attendance_student" />
        @endforeach
    </div>
    @else
    <div> 
        <p class="flex justify-center">{{__('vocab.empty')}}</p>
    </div>
    @endunless