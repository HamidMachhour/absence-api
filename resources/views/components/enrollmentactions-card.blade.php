@props(['enrollment'])
<div class="btn-group gap-2">
    <a href="{{ route('enrollments.accept', ['enrollment' => $enrollment->id]) }}"
        class="btn bg-success">
            <i class="fas fa-check"></i>
    </a>
    <form action="{{ route('enrollments.destroy', ['enrollment' => $enrollment->id]) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn bg-danger"
            onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
            <i class="fa-solid fa-trash"></i>
        </button>
    </form>
</div>
