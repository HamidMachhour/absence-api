@props(['seance'])
@auth
<div class="btn-group m-1">

    @if (auth()->user()->id === $seance->teacherId)

    <a href="{{ route('attendances.checklist', ['seance' => $seance]) }}" class="btn btn-app mx-1 bg-warning">
        <i class="fas fa-check"></i>
        {{__('vocab.record_absence')}}
    </a>

    <a href="{{route('misconduct_reports.studentList', ['seance' => $seance])}}" class="btn btn-app bg-info">
        <i class="fas fa-list"></i>
        {{__('vocab.record_report')}}
    </a>
    @elseif (auth()->user()->hasRole('admin'))
    <form action="{{ route('seances.destroy', ['seance' => $seance->id]) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-app mx-1 bg-danger"
            onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
            <i class="fas fa-trash"></i>
            {{__('vocab.delete')}}
        </button>
    </form>
    <a href="{{ route('seances.edit', ['seance' => $seance->id]) }}" class="btn btn-app mx-1 bg-primary">
        <i class="fas fa-edit"></i>
        {{__('vocab.edit')}}
    </a>
    @endif
</div>
@endauth