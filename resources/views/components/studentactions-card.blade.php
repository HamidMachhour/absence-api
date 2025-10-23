@props(['student'])
@auth
    <div class="btn-group mx-1">
        <a class="btn btn-app bg-warning mx-1" href="{{ route('students.card', ['student' => $student->id]) }}">
            <i class="far fa-address-card"></i> {{__('vocab.card')}} 
        </a>
        <a class="btn btn-app bg-info mx-1" href="{{ route('students.edit', ['student' => $student->id]) }}">
            <i class="fas fa-edit"></i> {{__('vocab.edit')}}
        </a>
        <form action="{{ route('students.destroy', ['student' => $student->id]) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-app bg-danger mx-1"
                onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                <i class="fas fa-trash"></i>{{__('vocab.delete')}}
            </button>
        </form>
        {{-- <a href="#" class="btn btn-app bg-info mx-1">
            <i class="fas fa-message"></i>
            دردشة
        </a>
        <div class="btn-group mx-1">
            <a href="#" class="btn btn-app bg-warning mx-1">
                <i class="fas fa-person-circle-plus"></i>
                تتبع
            </a>
        </div> --}}
    </div>

@endauth
