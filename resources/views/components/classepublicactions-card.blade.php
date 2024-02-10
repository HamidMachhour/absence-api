@props(['classe'])

<div class="btn-group mx-1">
    <a class="btn btn-app mx-1" href="{{ route('students.index') }}?classeId={{ $classe->id }}">
        <span class="badge bg-purple">{{ count($classe->students) }}</span>
        <i class="fas fa-users"></i> تلاميد
    </a>
    <a class="btn btn-app mx-1" href="{{ route('seances.index') }}?classeId={{ $classe->id }}">
        <span class="badge bg-danger">{{ count($classe->seances) }}</span>
        <i class="fas fa-clock"></i>الحصص
    </a>

</div>
