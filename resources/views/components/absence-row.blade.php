@props(['absence'])

<div class="card card-primary card-outline m-1" :title="">

    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
        <div class="card-title h3">
            <a href="{{ request()->fullUrlWithQuery(['dayId' => $absence->seance->dayId, 'page' => 1]) }}"
                class="flex text-blue-600 hover:text-gray-700 justify-center">
                {{ $absence->seance->day->dayName() .
                    ' ( ' .
                    ($de = new \DateTime($absence->seance->de))->format('H:i') .
                    ' - ' .
                    ($a = new \DateTime($absence->seance->a))->format('H:i') .
                    ' ) ' }}
            </a>
        </div>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-1">

        <div class="d-flex flex-column">
            <div>
                <a href="{{ request()->fullUrlWithQuery(['classeId' => $absence->seance->classeId, 'page' => 1]) }}"
                    class="flex text-gray-700 hover:text-blue-600 justify-center">
                    {{ $absence->seance->classe->classeName() }}
                </a>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['subjectId' => $absence->seance->subjectId, 'page' => 1]) }}"
                    class="flex text-blue-600 hover:text-gray-700 justify-center">
                    {{ $absence->seance->subject->subjectName() }}
                </a>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['teacherId' => $absence->seance->teacherId, 'page' => 1]) }}"
                    class="flex text-gray-700 hover:text-blue-600 justify-center">
                    {{ $absence->seance->teacher->fullName()  }}
                </a>
            </div>
            <a href="{{ request()->fullUrlWithQuery(['salle' => $absence->seance->salle, 'page' => 1]) }}">
                <div class="flex gap-2 items-center text-blue-600 hover:text-gray-700 justify-center">
                    <div>{{ $absence->seance->salle }}</div>
                    <div>{{__('vocab.room')}}</div>
                </div>
            </a>
            <a
                href="{{ request()->fullUrlWithQuery(['date_absence' => $absence->created_at->format('Y-m-d'), 'page' => 1]) }}">
                <div class="flex text-gray-700 hover:text-blue-600 justify-center">
                    <div>{{ $absence->created_at->format('Y-m-d H:i')  }}</div>
                    <div>:{{__('vocab.date')}}</div>
                </div>
            </a>
            <div class="btn-group gap-2 ">
                @auth
                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                        <form action="{{ route('absences.destroy', ['absence' => $absence]) }}" method="POST"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn bg-danger" onclick="return confirm('{{__('vocab.delete_permanently').'?'}}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    @endif
                @endauth
                <a href="{{ route('absences.show', ['absence' => $absence]) }}">
                    <div class="btn bg-success">
                        {{ $absence->absenceStudents->count() }}
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
