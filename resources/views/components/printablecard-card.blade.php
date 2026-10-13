@props(['student'])
<div class="card card-primary card-outline printable-card-class student-id-card">
    <div class="card-body p-1">
        <div class="row align-items-stretch g-0">

            <div class="col-5 d-flex flex-column align-items-center text-center pe-2">
                <img class="student-img img-thumbnail rounded-circle"
                    src="{{ $student->studentImage->uri ?? asset('/assets/images/no-image.png') }}"
                    alt="Student Image"
                    style="width: 80px; height: 80px; object-fit: cover;">

                <h5 class="mt-2 mb-0 text-primary lh-sm">{{ $student->user->fullName() }}</h5>
                <div class="fw-semibold">{{ \Illuminate\Support\Str::before($student->user->email, '@') }}</div>
                <small class="text-muted">{{ $student->user->code }}</small>
            </div>

            <div class="col-7 d-flex flex-column align-items-center justify-content-between p-0">
                <img class="qr-code" src="{{ $student->qrCodeImageUrl() }}" alt="QR Code"
                    style="width: 230px; height: 230px;">
                <div class="d-flex justify-content-between w-100 px-2 fw-semibold">
                    <span>{{ $student->classe->classeName() }}</span>
                    <span>{{ $student->schoolYear() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
