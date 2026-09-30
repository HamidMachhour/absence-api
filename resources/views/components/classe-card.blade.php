@props(['classe'])

<div class="card card-primary card-outline collapsed-card" >
    <div class="card-header d-flex gap-2  {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}">
        <h3 class="card-title">{{ $classe->classeName() }}</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        <!-- /.card-tools -->
    </div>
    <!-- /.card-header -->
    <div class="card-body" >
        <div class="d-flex flex-wrap justify-content-lg-center">
            <div class="col-sm-6">
                <div class="position-relative">

                    <a href="{{ route('classeImages.index', ['classe' => $classe->id]) }}" class="image">
                        <img src="{{ $classe->classeImage->uri ?? asset('/assets/images/no-image-class.jpg') }}" alt="{{ $classe->classeName() }}"
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

            <x-classe-tags :tagsCsv="$classe->tags" />


            <div class="user-panel d-flex mb-1">
                <div class="image">
                    <img src="{{ $classe->user->userImage->uri ?? asset('/assets/images/no-image.png') }}"
                        class="img-circle elevation-2"
                        alt="{{ $username = $classe->user->fullName()  }}">
                </div>
                <div class="info">
                    <a href="{{ request()->fullUrlWithQuery(['userId' => $classe->userId, 'page' => 1]) }}"
                        class="d-block">{{ $username }}</a>
                </div>
            </div>
            @auth
                @if (auth()->user()->hasRole('admin') || auth()->user()->id === $classe->userId)
                    <x-classeactions-card :classe="$classe" />
                @elseif(auth()->user()->isStudentCandidate())
                    <x-classestudentactions-card :classe="$classe" />
                @else
                    <x-classepublicactions-card :classe="$classe" />
                @endif
            @else
                <x-classepublicactions-card :classe="$classe" />
            @endauth
        </div>
    </div>
    <!-- /.card-body -->
</div>
