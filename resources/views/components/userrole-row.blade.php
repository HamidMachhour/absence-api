@props(['userRole'])
<tr>
    <td>
        {{ $userRole->created_at }}
    </td>
    <td>
        {{ $userRole->roleName() }}
    </td>
    <td>
        <div class='btn-group gap-2'>
            <form action="{{ route('userRoles.destroy', ['userRole' => $userRole->id]) }}" method="POST"
                class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-block bg-danger"
                    onclick="return confirm('{{__('vocab.are_you_sure')}}')">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>