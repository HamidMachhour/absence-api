@props(['student'])


<x-card class="d-flex flex-column justify-stretch text-xs">
    @auth
        <a href="{{ route('students.edit', ['student' => $student->id]) }}"
            class="flex justify-center bg-green-700 text-white mb-2 p-2 rounded-xl hover:opacity-80">
            <div classs="block">
                <i class="fa-solid fa-edit"></i>
                تعديل
            </div>
        </a>
        <form action="{{ route('students.destroy', ['student' => $student->id]) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full bg-red-500 text-white  mb-2 p-2 rounded-xl hover:opacity-80"
                onclick="return confirm('Are you sure you want to delete this student?')">
                <i class="fa-solid fa-trash"></i>
                حدف
            </button>
        </form>
        <a href="#" class="flex justify-center bg-blue-500 text-white mb-2 p-2 rounded-xl hover:opacity-80">
            <div class="block">
                <i class="fa-solid fa-message"></i>
                دردشة
            </div>
        </a>
    @endauth

</x-card>
