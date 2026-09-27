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
            <div id="qr-reader" class="w-full max-w-sm"></div>
            <p id="scanner-error" class="text-danger text-sm font-medium hidden"></p>

            <!-- Flash Message -->
            @if(session('message'))
            <p class="text-info font-medium">{{ session('message') }}</p>
            @endif
        </div>
    </div>
</div>

<div id="attendance-list-container">
    @include('attendances.attendance_students',['attendance'=>$attendance])
</div>
<!-- Audio Elements for Feedback -->
<audio id="successSound" src="{{ asset('assets/sounds/success.mp3') }}" preload="auto"></audio>
<audio id="failureSound" src="{{ asset('assets/sounds/failure.mp3') }}" preload="auto"></audio>

@endsection

@section('scripts')
<!-- <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script> -->

<script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>

<!-- Load QRCode.js -->
<!-- <script src="https://cdn.jsdelivr.net/npm/qrcodejs@latest/qrcode.min.js"></script> -->

<script src="{{ asset('assets/js/qrcode.min.js') }}" ></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const scannerError = document.getElementById('scanner-error');
        const localhostHosts = ['localhost', '127.0.0.1', '::1'];
        const isLocalhost = localhostHosts.includes(window.location.hostname);

        const setScannerError = (message) => {
            if (!scannerError) return;
            scannerError.textContent = message;
            scannerError.classList.remove('hidden');
        };

        // Get references to the audio elements once the DOM is ready
        const successAudio = document.getElementById('successSound');
        const failureAudio = document.getElementById('failureSound');

        // Helper function to play a specific sound
        function playSound(audioElement) {
            if (audioElement) {
                // Stop and rewind the audio before playing to allow for rapid, sequential scans
                audioElement.pause();
                audioElement.currentTime = 0;
                audioElement.play().catch(error => {
                    // Catch the common 'The play() request was interrupted' error 
                    // or if the browser blocks autoplay before user interaction.
                    console.warn('Audio playback failed:', error);
                });
            }
        }

        // --- QR Code Scanner Logic (using Html5QrcodeScanner) ---
        const qrCodeSuccessCallback = async (decodedText, decodedResult) => {
            if (isProcessing) return;
            isProcessing = true;

            const url =
                `{{ route('attendances.popStudent', ['attendance' => $attendance, 'scannedData' => 'REPLACE_ME']) }}`
                .replace('REPLACE_ME', encodeURIComponent(decodedText));
            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.status === 200) {
                    // Status 200: Student was popped, list still exists.
                    await refreshAttendanceList();
                    playSound(successAudio);
                } else if (response.status === 201) {
                    // Status 201: Last student was popped, attendance record was deleted.
                    // Redirect to the seances index page as this attendance list is gone.
                    playSound(successAudio);
                    window.location.href = "{{ route('seances.index', ['teacherId' => auth()->user()->id]) }}";
                } else {
                    // Handle other errors (403, 404, etc.)
                    const data = await response.json();
                    console.error('Error:', data.message);
                    playSound(failureAudio);
                }
            } catch (error) {
                console.error('Error processing scan:', error);
                playSound(failureAudio);
            } finally {
                // Add a small delay before allowing the next scan
                setTimeout(() => {
                    isProcessing = false;
                }, 1500);
            }
        };

        let cameraErrorReported = false;
        const qrCodeErrorCallback = (errorMessage) => {
            // This callback is called when a QR code is not found or other errors occur.
            // We can often ignore it to allow for continuous scanning.
            if (cameraErrorReported) return;

            const msg = String(errorMessage || '').toLowerCase();
            if (msg.includes('permission') || msg.includes('notallowed') || msg.includes('insecure')) {
                cameraErrorReported = true;
                setScannerError('Camera access failed. If you are using a LAN URL (http://10.0.0.11:8000), open the page over HTTPS or localhost to allow camera streaming.');
            }
        };

        let isProcessing = false;

        async function refreshAttendanceList() {
            try {
                const response = await fetch("{{ route('attendances.students.list', ['attendance' => $attendance]) }}", { // prettier-ignore
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // --- ADDED: Check for successful HTTP status (200-299) ---
                if (!response.ok) {
                    // Read the response text to get the HTML error page content for debugging
                    const errorText = await response.text();
                    console.error(`HTTP Error! Status: ${response.status}`, errorText.substring(0, 100) + '...');
                    throw new Error(`Server returned HTTP status ${response.status}`);
                }

                // --- The rest of your original logic proceeds only if status is OK ---
                const data = await response.json(); // This will now only execute on a 200-299 status
                document.getElementById('attendance-list-container').innerHTML = data.html;
                document.getElementById('attendance-count').textContent = data.count;

            } catch (error) {
                console.error('Error refreshing list:', error);
            }
        }
        if (!window.isSecureContext && !isLocalhost) {
            setScannerError('Camera is blocked on insecure HTTP origins. Use HTTPS for this host, or open via localhost.');
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setScannerError('This browser does not expose camera APIs in the current context.');
            return;
        }

        // Check if the library is loaded
        if (typeof Html5QrcodeScanner !== 'undefined') {
            const html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", // ID of the container element
                {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    },
                    rememberLastUsedCamera: true,
                },
                /* verbose= */
                false
            );
            html5QrcodeScanner.render(qrCodeSuccessCallback, qrCodeErrorCallback);
        } else {
            console.error('html5-qrcode library failed to load.');
            setScannerError('QR Scanner library failed to load. Please check your connection and try again.');
        }

        // --- Share Modal QR Code Logic ---
        const shareButton = document.getElementById('shareButton');
        const qrModal = document.getElementById('qrModal');
        const closeQrModal = document.getElementById('closeQrModal');
        const qrCodeDiv = document.getElementById('qrcode');

        let qrCodeInstance = null;

        shareButton.addEventListener('click', function() {
            qrModal.classList.remove('hidden');

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
    });
</script>

@endsection