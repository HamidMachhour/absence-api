@props(['subject'])
<tr>
    <td>
        {{ $subject->created_at }}
    </td>
    <td>
        {{ $subject->subjectName() }}
    </td>
    <td>
        <div class='btn-group gap-2'>
            <a href="{{ route('subjects.edit', ['subject' => $subject->id]) }}" class="btn btn-app mx-1 bg-primary">
                <i class="fas fa-edit"></i>
                {{__('vocab.edit')}}
            </a>
            <form action="{{ route('subjects.destroy', ['subject' => $subject->id]) }}" method="POST"
                class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-app mx-1 bg-danger"
                    onclick="return confirm('{{__('vocab.are_you_sure')}}')">
                    <i class="fas fa-trash"></i>
                    {{__('vocab.delete')}}
                </button>
            </form>

        </div>
    </td>
</tr>