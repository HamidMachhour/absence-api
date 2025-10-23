@props(['proposed_action'])
<tr>
    <td>
        {{ $proposed_action->actionName() }}
    </td>
    <td>
        <div class='btn-group gap-2'>
            <a href="{{ route('proposed_actions.edit', ['proposed_action' => $proposed_action->id]) }}" class="btn btn-app mx-1 bg-primary">
                <i class="fas fa-edit"></i>
                {{__('vocab.edit')}}
            </a>
            <form action="{{ route('proposed_actions.destroy', ['proposed_action' => $proposed_action->id]) }}" method="POST"
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