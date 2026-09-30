@props(['classe'])

@if (auth()->user()->hasRole('admin') || $classe->userId === auth()->user()->id)
    <div class="btn-group gap-2">
        <a href="{{ route('students.cards', ['classe' => $classe->id]) }}" class="btn btn-app bg-info">
            <i class="far fa-address-card"></i>
            {{__('vocab.cards')}}
        </a>
        <a href="{{ route('classes.importForm', ['classe' => $classe->id]) }}" class="btn btn-app bg-warning">
            <i class="fas fa-arrow-down"></i>
            {{__('vocab.import')}}
        </a>
        <a href="{{ route('classes.export', ['classe' => $classe->id]) }}" class="btn btn-app bg-orange">
            <i class="fas fa-arrow-up"></i>
            {{__('vocab.export')}}
        </a>
        <a href="{{ route('classes.edit', ['classe' => $classe->id]) }}" class="btn btn-app bg-info">
            <i class="fas fa-edit"></i>
            {{__('vocab.edit')}} {{__('vocab.class')}}
        </a>
       
        <form action="{{ route('classes.empty', ['classe' => $classe->id]) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-app bg-secondary"
                onclick="return confirm('{{__('vocab.are_you_sure_empty_class').' ?'}}')">
                <i class="fas fa-broom"></i>
                {{__('vocab.empty')}} {{__('vocab.class')}}
            </button>
        </form>

         <form action="{{ route('classes.destroy', ['classe' => $classe->id]) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-app bg-danger"
                onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                <i class="fas fa-trash"></i>
                {{__('vocab.delete')}} {{__('vocab.class')}}
            </button>
        </form>
    </div>
@endif
