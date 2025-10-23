@props(['classe'])

<x-card :title='$classe->classeName()'>
    <div class="d-flex flex-row justify-content-lg-center">
        <div class="col-sm-2 mb-2">

            <div class="position-relative">



                <a href="{{ route('classeImages.index', ['classe' => $classe->id]) }}">
                    <img src="{{ $classe->classeImage->uri ?? asset('/assets/images/no-image.png') }}" alt="Photo 1"
                        class="img-fluid">
                </a>
                <div class="ribbon-wrapper ribbon">
                    <div class="ribbon bg-success">
                        <a href="{{ route('classes.show', ['classe' => $classe->id]) }}">
                            {{ $classe->classeName() }}
                        </a>
                    </div>
                </div>
            </div>
        </div>



        <div>
            <div class="btn-group mx-1">
                <a class="btn btn-app mx-2" href="{{ route('students.index') }}?classeId={{ $classe->id }}">
                    <span class="badge bg-purple">{{ count($classe->students) }}</span>
                    <i class="fas fa-users"></i> {{__('vocab.students')}} 
                </a>
                <a class="btn btn-app mx-2" href="{{ route('seances.index') }}?classeId={{ $classe->id }}">
                    <span class="badge bg-danger">{{ count($classe->seances) }}</span>
                    <i class="fas fa-clock"></i>{{__('vocab.time_quotas')}} 
                </a>
            </div>
            <div class="user-panel pb-3 d-flex">
                <div class="image">
                    <img src="{{ $classe->user->userImage->uri ?? asset('/assets/images/no-image.png') }}"
                        class="img-circle elevation-2"
                        alt="{{ $username = $classe->user->fullName()  }}">
                </div>
                <div class="info">
                    <a href="{{ route('users.show', ['user' => $classe->userId]) }}"
                        class="d-block">{{ $username }}</a>
                </div>
            </div>
        </div>

    </div>

</x-card>
