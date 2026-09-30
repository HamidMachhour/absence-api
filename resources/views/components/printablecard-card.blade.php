@props(['student'])
<div class="card card-primary card-outline printable-card-class student-id-card">
    <div class="card-body p-2">
        <div class="row align-items-center">
            
            <div class="col-4 d-flex flex-column align-items-center text-center border-end pe-3">
                
                <img class="student-img img-thumbnail"
                    src="{{ $student->studentImage->uri ?? asset('/assets/images/no-image.png') }}"
                    alt="Student Image" 
                    style="width: 80px; height: 80px; object-fit: cover;">
                
                <img class="qr-code" 
                    src="{{  $student->qrCodeImageUrl() }}" 
                    alt="QR Code" 
                    style="width: 100px; height: 100px;">
                <small class="text-muted">{{ $student->user->code }}</small>
            </div>
            
            <div class="col-8 ps-3">
                <h4 class="mb-1 text-primary">
                    {{ $student->user->fullName() }}
                </h4>
                <hr class="mt-1 mb-1">
                
                <div class="d-flex justify-content-between mb-2">
                    <p class="mb-0">
                        <i class="fas fa-chalkboard-teacher fa-fw me-2 text-success"></i>
                        {{ $student->classe->classeName() }}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-sort-numeric-up fa-fw me-2 text-secondary"></i>
                        {{ $student->num }}
                    </p>
                </div>
                
                <div class="border-top pt-2">
                    <p class="mb-1 text-sm">
                        <i class="fas fa-calendar-alt fa-fw me-2 text-info"></i>
                        {{ $student->schoolYear() }}
                    </p>
                    <p class="mb-0 text-sm">
                        <i class="fas fa-envelope fa-fw me-2 text-info"></i>
                        {{ $student->user->email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>