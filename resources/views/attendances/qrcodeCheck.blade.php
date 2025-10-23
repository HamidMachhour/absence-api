@extends('layouts.master')

@section('title')
{{__('vocab.absence_list')}}
@endsection

@section('css')
@endsection

@section('main-page')
<a href="{{ route('seances.index', ['teacherId' => auth()->user()]) }}">{{__('vocab.time_quotas')}} </a>
@endsection

@section('page')
{{__('vocab.absence_list')}}
@endsection

@section('content')
<div class="space-y-4">
    <!-- Attendance Summary -->
    <div class="small-box bg-warning mx-2 shadow rounded-lg">
        <div class="inner">
            <h3 id="attendance-count">{{ $attendance->attendanceStudents->count() }}</h3>
            <p>{{ __('vocab.absence_list') }}</p>
        </div>
        <div class="icon">
            <i class="fas fa-person-running"></i>
        </div>

        <div class="flex justify-start mx-2">
            <!-- Go back -->
            <div class="flex justify-start mx-2">
                <a href="{{ route('attendances.show', ['attendance' => $attendance]) }}"
                    class="btn btn-app bg-info">
                    {{ __('vocab.go_back') }}
                </a>
            </div>

            <!-- Share -->
            <div class="flex justify-start mx-2">
                <button id="shareButton" class="btn btn-app bg-gray">
                    {{ __('vocab.share') }}
                </button>
            </div>
            <x-seance-ro-card :seance="$attendance->seance" />

        </div>
    </div>

    <!-- Popup Modal -->
    <div id="qrModal"
        class="fixed inset-0 z-50 bg-black bg-opacity-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg relative flex flex-col items-center p-6 w-full max-w-lg">
            <!-- Close button -->
            <button id="closeQrModal"
                class="absolute top-2 right-2 text-gray-600 text-2xl leading-none">&times;</button>

            <!-- Title -->
            <h2 class="text-xl font-bold mb-4 text-center">{{ __('vocab.share') }}</h2>

            <!-- QR Code Container -->
            <div id="qrcode" class="flex justify-center mb-4 w-full"></div>

            <!-- Instructions -->
            <p class="text-center text-sm text-gray-500">
                Scan this QR code to open this page on your device
            </p>
        </div>
    </div>


    <!-- QR Scanner Dialog -->
    <div id="dialog" class="mx-4 p-4 bg-white rounded-lg shadow-md">
        <div class="flex flex-col items-center space-y-3">
            <!-- Video Preview -->
            <video id="preview" class="w-1/12 max-w-[120px] rounded border shadow"></video>

            <!-- Flash Message -->
            @if(session('message'))
            <p class="text-info font-medium">{{ session('message') }}</p>
            @endif

            <!-- Scan Controls -->
            <div class="flex space-x-3">
                <!-- Start Scan -->
                <button id="scanButton" class="btn btn-app bg-info">
                    <i class="fas fa-qrcode"></i>
                    {{ __('vocab.confirm') }} {{ __('vocab.present') }}
                </button>

                <!-- Stop Scan -->
                <button id="stopScanButton" class="btn btn-app bg-danger" style="display:none;">
                    <i class="fas fa-stop"></i>
                    {{ __('vocab.stop') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="attendance-list-container">
    @include('attendances.attendance_students',['attendance'=>$attendance])
</div>
@endsection

@section('scripts')
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>


<script>
    let isProcessing = false;
    let scanner = new Instascan.Scanner({
        video: document.getElementById('preview')
    });

    const scanButton = document.getElementById('scanButton');
    const stopScanButton = document.getElementById('stopScanButton');

    // Function to handle the AJAX refresh of the attendance list
    function refreshAttendanceList() {
        fetch("{{ route('attendances.students.list', ['attendance' => $attendance]) }}", {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('attendance-list-container').innerHTML = data.html;
                document.getElementById('attendance-count').textContent = data.count;
            })
            .catch(error => console.error('Error refreshing list:', error));
    }


    // Start Scan
    scanButton.addEventListener('click', function() {
        scanner.addListener('scan', function(content) {
            if (isProcessing) return;
            isProcessing = true;

            const url = `{{ route('attendances.popStudent', ['attendance' => $attendance, 'scannedData' => 'REPLACE_ME']) }}`
                .replace('REPLACE_ME', encodeURIComponent(content));

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        refreshAttendanceList();
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    setTimeout(() => {
                        isProcessing = false;
                    }, 1500);
                });
        });

        Instascan.Camera.getCameras()
            .then(function(cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[1] || cameras[0]);
                    // Toggle buttons
                    scanButton.style.display = "none";
                    stopScanButton.style.display = "inline-block";
                } else {
                    console.error('No cameras found.');
                }
            })
            .catch(function(e) {
                console.error(e);
            });
    });

    // Stop Scan
    stopScanButton.addEventListener('click', function() {
        scanner.stop()
            .then(() => {
                console.log("Scanner stopped.");
                // Toggle buttons
                stopScanButton.style.display = "none";
                scanButton.style.display = "inline-block";
            })
            .catch(err => console.error("Error stopping scanner:", err));
    });
</script>

<!-- Load QRCode.js -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
    const shareButton = document.getElementById('shareButton');
    const qrModal = document.getElementById('qrModal');
    const closeQrModal = document.getElementById('closeQrModal');
    const qrCodeDiv = document.getElementById('qrcode');

    let qrCodeInstance = null;

    shareButton.addEventListener('click', function() {
        // Show modal
        qrModal.classList.remove('hidden');

        // Clear old QR code if exists
        qrCodeDiv.innerHTML = "";

        // Generate QR code for current URL
        qrCodeInstance = new QRCode(qrCodeDiv, {
            text: window.location.href,
            width: 300, // increase width
            height: 300, // increase height
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

    });

    closeQrModal.addEventListener('click', function() {
        qrModal.classList.add('hidden');
    });

    // Close modal when clicking outside content
    qrModal.addEventListener('click', function(e) {
        if (e.target === qrModal) {
            qrModal.classList.add('hidden');
        }
    });
</script>

@endsection