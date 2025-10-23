@props(['misconduct_report'])
@auth
<div class="btn-group m-1">
    <a href="{{ route('misconduct_reports.show', ['misconduct_report' => $misconduct_report->id]) }}" class="btn btn-app mx-1 bg-info">
        <i class="fas fa-eye"></i>
        {{__('vocab.show')}}
    </a>

    <a href="{{ route('misconduct_reports.print', ['misconduct_report' => $misconduct_report->id]) }}" class="btn btn-app mx-1 bg-warning">
        <i class="fas fa-print"></i>
        {{__('vocab.print')}}
    </a>

    <a href="{{ route('misconduct_reports.edit', ['misconduct_report' => $misconduct_report->id]) }}" class="btn btn-app mx-1 bg-primary">
        <i class="fas fa-edit"></i>
        {{__('vocab.edit')}}
    </a>

    <form action="{{ route('misconduct_reports.destroy', ['misconduct_report' => $misconduct_report->id]) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-app mx-1 bg-danger"
            onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
            <i class="fas fa-trash"></i>
            {{__('vocab.delete')}}
        </button>
    </form>
</div>
@endauth