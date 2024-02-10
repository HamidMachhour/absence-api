@props(['student', 'seance'])
<x-card>
    <div class="grid grid-cols-3 gap-1 mb-2">

        @auth
            @if (auth()->user()->id === $seance->profId)
                <form method="POST" action="{{ route('appels.pushStudent') }}">
                    @csrf
                    <input type="hidden" name="studentId" value="{{ $student->id }}">
                    <input type="hidden" name="seanceId" value="{{ $seance->id }}">

                    <button type="submit" class="block  bg-orange-600 text-white mb-2 p-2 rounded-xl hover:opacity-80">
                        <i class="fa-solid fa-hand">غائب</i>
                    </button>
                </form>
            @endif
        @endauth
        <div class="d-flex text-green-600 hover:text-orange-700">
            <a href="{{route('students.show',['student'=>$student->id])}}">
                {{ $student->num }}
                <span> - <span>
                        {{ $student->user->firstName }}
                        <span>
                            {{ $student->user->lastName }}
            </a>
        </div>
        <div class="d-flex flex-wrap rounded-xl w-20">
            <img src="{{ $student->user->userImage->uri ?? asset('/images/no-image.png') }}" alt="" />
        </div>


    </div>
</x-card>
