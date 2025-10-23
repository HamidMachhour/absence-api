@props(['address'])

@auth
    <div class="btn-group m-1">

        @if ($address->userId === auth()->user()->id)
            <a href="{{ route('addresses.edit', ['address' => $address->id]) }}"
                class="btn btn-app mx-1 bg-primary">
                <i class="fa-solid fa-edit"></i>
                {{__('vocab.edit')}}
            </a>
            @if ($address->id !== auth()->user()->addressId)
                <form action="{{ route('addresses.default', ['address' => $address->id]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-app mx-1 bg-info"
                        onclick="return confirm('{{__('vocab.are_you_sure').'?'}}')">
                        <i class="fa-solid fa-check"></i>
                       {{__('vocab.set_as_primary_address')}} </button>
                </form>
            @else
                <div class="btn btn-app mx-1">
                    <i class="fa-solid fa-check"></i>
                    {{__('vocab.primary_address')}}
                </div>
            @endif
            <form action="{{ route('addresses.destroy', ['address' => $address->id]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-app mx-1 bg-danger"
                    onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                    <i class="fa-solid fa-trash"></i>
                    {{__('vocab.delete')}}
                </button>
            </form>
        @endif
    </div>
@endauth
