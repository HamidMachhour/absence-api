@props(['classe'])

<div class="btn-group mx-1">
    <a class="btn btn-app bg-gray mx-2" href="{{ route('students.index') }}?classeId={{ $classe->id }}">
        <span class="badge bg-purple">{{ count($classe->students) }}</span>
        <i class="fas fa-users"></i> {{__('vocab.students')}} 
    </a>
    <a class="btn btn-app bg-gray mx-2" href="{{ route('seances.index') }}?classeId={{ $classe->id }}">
        <span class="badge bg-danger">{{ count($classe->seances) }}</span>
        <i class="fas fa-clock"></i>{{__('vocab.time_quotas')}} 
    </a>
    <form action="{{ route('enrollments.store') }}" method="POST" class="inline">
        <input hidden name="userId" value="{{ auth()->user()->id }}" />
        <input hidden name="classeId" value="{{ $classe->id }}" />
        @csrf
        <button type="submit" class="btn btn-app bg-info mx-2"
            onclick="return confirm('{{__('vocab.are_you_sure').'?'}})">
            <i class="fas fa-add"></i>
            {{__('vocab.enroll')}}
        </button>
    </form>
</div>
