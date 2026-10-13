@extends('layouts.master')

@section('title')
{{__('vocab.absence_list')}}
@endsection

@section('css')
@endsection

@section('main-page')
@endsection

@section('page')
{{__('vocab.absence_list')}}
@endsection

@section('content')
<div class="space-y-4">
    <!-- Salle Selection Form -->
    <div class="mx-2 p-4 bg-white rounded-lg shadow-md">
        <form id="salle-form" action="{{ route('terminal.find') }}" method="GET" class="flex items-center space-x-2">
            <label for="salle" class="font-medium">{{ __('vocab.room') }}:</label>
            <input type="number" id="salle" name="salle" class="form-input w-full rounded-md shadow-sm"
                placeholder="{{ __('vocab.room') }}" required>
            <button type="submit"
                class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                {{ __('vocab.search') }}
            </button>
        </form>
    </div>

    <!-- Attendance Summary -->
    <div class="small-box bg-warning mx-2 shadow rounded-lg">
        <div class="inner">
            <h3 id="attendance-count">{{ $attendance->attendanceStudents->count() }}</h3>
            <p>{{ __('vocab.absence_list') }}</p>
        </div>
        <div class="icon">
            <i class="fas fa-person-running"></i>
        </div>
    </div>


    <!-- QR Scanner Dialog -->
    <div id="dialog" class="mx-4 p-4 bg-white rounded-lg shadow-md">
        <div class="flex flex-col items-center space-y-3">
            @if(!($terminalReadOnly ?? false))
                <!-- Video Preview -->
                <div id="qr-reader" class="w-full max-w-sm"></div>
            @else
                <p class="text-muted">{{ __('vocab.no_attendance_data') }} (read-only finalized absence view)</p>
            @endif

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

@php
    $terminalReadOnlyFlag = $terminalReadOnly ?? false;
    $popStudentBaseUrlValue = $terminalReadOnlyFlag
        ? null
        : route('attendances.popStudent', ['attendance' => $attendance, 'scannedData' => 'REPLACE_ME']);
    $refreshAttendanceUrlValue = $terminalReadOnlyFlag
        ? null
        : route('attendances.students.list', ['attendance' => $attendance]);
@endphp

<script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>

<script src="{{ asset('assets/js/qrcode.min.js') }}"></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {

    const terminalReadOnly = {!! json_encode($terminalReadOnlyFlag) !!};
    const popStudentBaseUrl = {!! json_encode($popStudentBaseUrlValue) !!};
    const refreshAttendanceUrl = {!! json_encode($refreshAttendanceUrlValue) !!};
    const scannerLoadErrorText = {!! json_encode(__('vocab.qr_scanner_load_error')) !!};

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
            if (terminalReadOnly) return;
            if (isProcessing) return;
            isProcessing = true;

            const url = popStudentBaseUrl.replace('REPLACE_ME', encodeURIComponent(decodedText));
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

        const qrCodeErrorCallback = (errorMessage) => {
            // This callback is called when a QR code is not found or other errors occur.
            // We can often ignore it to allow for continuous scanning.
        };

        let isProcessing = false;
        let autoRefreshInterval = null;

        async function refreshAttendanceList() {
            try {
                if (!refreshAttendanceUrl) return;

                const response = await fetch(refreshAttendanceUrl, {
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

        /**
         * Check if the current seance is over and a next attendance is available.
         * If next attendance exists, refresh the page to load it.
         */
        async function checkAndLoadNextAttendance() {
            try {
                const salle = "{{ $salle }}";
                const response = await fetch(
                    "{{ route('terminal.checkNext') }}" + "?salle=" + encodeURIComponent(salle),
                    {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                if (response.ok) {
                    const data = await response.json();
                    if (data.next_exists) {
                        // Next attendance is available, refresh the page
                        console.log('Next seance detected, reloading dashboard...');
                        window.location.reload();
                    }
                }
            } catch (error) {
                console.error('Error checking for next attendance:', error);
            }
        }

        /**
         * Start auto-refresh check every 10 seconds (10000ms).
         * This checks if a new seance has started and reloads if needed.
         */
        function startAutoRefreshCheck() {
            // Check every 10 seconds
            autoRefreshInterval = setInterval(checkAndLoadNextAttendance, 10000);
        }

        /**
         * Stop the auto-refresh check interval.
         */
        function stopAutoRefreshCheck() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }

        // Check if the library is loaded
        if (!terminalReadOnly && typeof Html5QrcodeScanner !== 'undefined') {
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

            // Start auto-refresh check when scanner is ready
            startAutoRefreshCheck();

            // Optional: Handle page visibility to pause/resume checks
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoRefreshCheck();
                } else {
                    startAutoRefreshCheck();
                }
            });
        } else if (!terminalReadOnly) {
            console.error('html5-qrcode library failed to load.');
            const errorContainer = document.getElementById('scanner-error'); // Assuming you might add this back
            if (errorContainer) {
                errorContainer.textContent = scannerLoadErrorText;
                errorContainer.classList.remove('hidden'); // Assuming you might add this back
            }
        }

        // --- Share Modal QR Code Logic ---
        const shareButton = document.getElementById('shareButton');
        const qrModal = document.getElementById('qrModal');
        const closeQrModal = document.getElementById('closeQrModal');
        const qrCodeDiv = document.getElementById('qrcode');

        let qrCodeInstance = null;

        if (shareButton && qrModal && closeQrModal && qrCodeDiv) {
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
        }
    });
</script>

@endsection