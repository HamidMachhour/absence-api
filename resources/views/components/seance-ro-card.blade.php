@props(['seance'])

<div class="card card-primary card-outline collapsed-card m-1" :title="">

    <div class="card-header">
        <div class="card-title h3">
            <a href="{{ request()->fullUrlWithQuery(['dayId' => $seance->dayId, 'page' => 1]) }}"
                class="flex text-blue-600 hover:text-gray-700 justify-center">
                {{ $seance->title() }}
            </a>
        </div>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-1">

        <div class="d-flex flex-column">
            <div>
                <a href="{{ request()->fullUrlWithQuery(['classeId' => $seance->classeId, 'page' => 1]) }}"
                    class="flex text-gray-700 hover:text-blue-600 justify-center">
                    {{ $seance->classe->classeName() }}
                </a>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['subjectId' => $seance->subjectId, 'page' => 1]) }}"
                    class="flex text-blue-600 hover:text-gray-700 justify-center">
                    {{ $seance->subject->subjectName() }}
                </a>
            </div>
            <div>
                <a href="{{ request()->fullUrlWithQuery(['teacherId' => $seance->teacherId, 'page' => 1]) }}"
                    class="flex text-gray-700 hover:text-blue-600 justify-center">
                    {{ $seance->teacher->fullName()  }}
                </a>
            </div>
            <a href="{{ request()->fullUrlWithQuery(['salle' => $seance->salle, 'page' => 1]) }}">
                <div class="flex gap-2 items-center text-blue-600 hover:text-gray-700 justify-center">
                    <i class="fa-solid fa-home"></i>
                    <div>{{ $seance->salle }}</div>
                    <div>{{__('vocab.room')}}</div>
                </div>
            </a>
            @auth
            <div class="btn-group m-1">
                @if (auth()->user()->id === $seance->teacherId)
                <a href="{{ route('attendances.checklist', ['seance' => $seance]) }}"
                    class="btn btn-app mx-1 bg-warning">
                    <i class="fas fa-check"></i>
                    {{__('vocab.record_absence')}}
                </a>
                <a href="{{route('misconduct_reports.studentList', ['seance' => $seance])}}" class="btn btn-app bg-info">
                    <i class="fas fa-list"></i>
                    {{__('vocab.record_report')}}
                </a>
                @endif
            </div>
            @endauth
        </div>

    </div>
</div>