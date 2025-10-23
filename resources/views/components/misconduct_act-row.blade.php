@props(['misconduct_act'])
<tr>
    <td>
        {{ $misconduct_act->actName() }}
    </td>
    <td>
        <div class='btn-group gap-2'>
            <a href="{{ route('misconduct_acts.edit', ['misconduct_act' => $misconduct_act->id]) }}" class="btn btn-app mx-1 bg-primary">
                <i class="fas fa-edit"></i>
                {{__('vocab.edit')}}
            </a>
            <form action="{{ route('misconduct_acts.destroy', ['misconduct_act' => $misconduct_act->id]) }}" method="POST"
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