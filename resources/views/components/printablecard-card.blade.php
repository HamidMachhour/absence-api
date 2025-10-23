@props(['student'])
<div class="card card-primary card-outline">
    <div class="background"></div>
    <div class="card-content">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 p-0 m-0 d-flex flex-column justify-content-start align-items-center">
                    <img class="qr-code" src="{{  $student->qrCodeImageUrl() }}" alt="QRCode" />
                </div>
                <div class="col-md-4 p-0 m-0 d-flex flex-column justify-content-start align-items-center">
                    <p class="info">{{ $student->user->code }}</p>
                    <p class="info">{{__('vocab.class')}} <br> {{ $student->classe->classeName() }}</p>
                    <p class="info">{{__('vocab.num')}} {{ $student->num }}</p>
                </div>
                <div class="col-md-4 p-0 m-0 d-flex flex-column justify-content-start align-items-center">
                    <img class="student-img"
                        src="{{ $student->studentImage->uri ?? asset('/assets/images/no-image.png') }}"
                        alt="Student Image">
                    <p class="info"><strong>{{ $student->user->fullName()  }}</strong>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 p-0 m-0">
                    <p class="info text-xs"> {{ $student->schoolYear() }}</p>
                </div>
                <div class="col-md-6 p-0 m-0">
                    <p class="info text-xs"> {{ $student->user->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
