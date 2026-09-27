@extends('layouts.master')

@section('title')
{{ $user->fullName() }} 
@endsection

@section('css')
@endsection

@section('main-page')
    <a href="{{ route('users.show', ['user' => $user]) }}">{{ $user->fullName() }} </a>
@endsection

@section('page')
{{__('vocab.profile_images')}}
@endsection

@section('content')
    <x-userinfo-card :user="$user" />
    @unless ($user_images->count() == 0)
        @auth
            @if ($user->id === auth()->user()->id || auth()->user()->hasRole('admin'))
                <div class=' mr-2'>
                    <a href="{{ route('userImages.create', ['user' => $user->id]) }}" class="btn btn-app bg-gray">
                        <i class="fas fa-plus"></i> {{__('vocab.add_image')}}
                    </a>
                    <button type="button" id="open-webcam-capture" class="btn btn-app bg-info">
                        <i class="fas fa-camera"></i> {{__('vocab.take_image')}}
                    </button>
                </div>
                <x-card :title="__('vocab.take_image')">
                    <div id="webcam-capture-card" class="hidden p-2">
                        <div class="mb-2 text-sm text-muted">{{__('vocab.take_image_description')}}</div>
                        <video id="webcam-video" class="w-full rounded-xl border" autoplay playsinline></video>
                        <canvas id="webcam-canvas" class="w-full rounded-xl border hidden mt-2"></canvas>
                        <div class="mt-2 flex gap-2 flex-wrap">
                            <button type="button" id="capture-webcam-image" class="btn btn-outline-primary">
                                <i class="fas fa-camera"></i> {{__('vocab.capture')}}
                            </button>
                            <button type="button" id="retake-webcam-image" class="btn btn-outline-secondary hidden">
                                <i class="fas fa-redo"></i> {{__('vocab.retake')}}
                            </button>
                            <button type="button" id="save-webcam-image" class="btn btn-outline-success hidden">
                                <i class="fas fa-save"></i> {{__('vocab.save')}}
                            </button>
                        </div>
                        <p id="webcam-error" class="text-red-500 text-xs mt-2 hidden"></p>
                    </div>
                </x-card>
                <x-card :title="__('vocab.images')">
                    <div class="lg:grid lg:grid-cols-2 gap-1 mx-1">
                        @foreach ($user_images as $user_image)
                            <div class="col-sm-12">
                                <div class="position-relative">
                                    <img src="{{ $user_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                                        class="img-fluid rounded-xl">
                                </div>
                                <div class="position-absolute right-1 top-0">
                                    <form action="{{ route('userImages.destroy', ['image' => $user_image->id]) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-block bg-danger"
                                            onclick="return confirm('{{__('vocab.are_you_sure')}}')">
                                            <i class="fas fa-cancel"></i>
                                        </button>
                                    </form>
                                    @if ($user->imageId !== $user_image->id)
                                        <form
                                            action="{{ route('userImages.setDefault', ['user' => $user->id, 'image' => $user_image->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-block bg-success"
                                                onclick="return confirm('{{__('vocab.are_you_sure')}}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <div class="btn btn-block bg-gray">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @else
                <div class="lg:grid lg:grid-cols-2 gap-2 mx-2">
                    @foreach ($user_images as $user_image)
                        <div class="position-relative">
                            <img src="{{ $user_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                                class="img-fluid rounded-xl">
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="lg:grid lg:grid-cols-2 gap-2 mx-2">
                @foreach ($user_images as $user_image)
                    <div class="position-relative">
                        <img src="{{ $user_image->uri ?? asset('/assets/images/no-image.png') }}" alt=""
                            class="img-fluid rounded-xl">
                    </div>
                @endforeach
            </div>
        @endauth
        <div class="mt-6 p-4">
            {{ $user_images->appends(request()->query->all())->links() }}
        </div>
    @else
        @if ($user->id === auth()->user()->id || auth()->user()->hasRole('admin'))
            <div>
                <a href="{{ route('userImages.create', ['user' => $user->id]) }}" class="btn btn-app bg-gray">
                    <i class="fas fa-plus"></i> {{__('vocab.add_image')}}
                </a>
                <button type="button" id="open-webcam-capture" class="btn btn-app bg-info">
                    <i class="fas fa-camera"></i> {{__('vocab.take_image')}}
                </button>
            </div>
            <x-card :title="__('vocab.take_image')">
                <div id="webcam-capture-card" class="hidden p-2">
                    <div class="mb-2 text-sm text-muted">{{__('vocab.take_image_description')}}</div>
                    <video id="webcam-video" class="w-full rounded-xl border" autoplay playsinline></video>
                    <canvas id="webcam-canvas" class="w-full rounded-xl border hidden mt-2"></canvas>
                    <div class="mt-2 flex gap-2 flex-wrap">
                        <button type="button" id="capture-webcam-image" class="btn btn-outline-primary">
                            <i class="fas fa-camera"></i> {{__('vocab.capture')}}
                        </button>
                        <button type="button" id="retake-webcam-image" class="btn btn-outline-secondary hidden">
                            <i class="fas fa-redo"></i> {{__('vocab.retake')}}
                        </button>
                        <button type="button" id="save-webcam-image" class="btn btn-outline-success hidden">
                            <i class="fas fa-save"></i> {{__('vocab.save')}}
                        </button>
                    </div>
                    <p id="webcam-error" class="text-red-500 text-xs mt-2 hidden"></p>
                </div>
            </x-card>
        @endif
        <p>{{__('vocab.no_data')}}</p>
    @endunless
 
@endsection

@section('scripts')
    <script>
        (() => {
            const openButton = document.getElementById('open-webcam-capture');
            const webcamCard = document.getElementById('webcam-capture-card');
            const video = document.getElementById('webcam-video');
            const canvas = document.getElementById('webcam-canvas');
            const captureButton = document.getElementById('capture-webcam-image');
            const retakeButton = document.getElementById('retake-webcam-image');
            const saveButton = document.getElementById('save-webcam-image');
            const errorMessage = document.getElementById('webcam-error');

            if (!openButton || !webcamCard || !video || !canvas || !captureButton || !retakeButton || !saveButton || !errorMessage) {
                return;
            }

            let stream = null;

            const setError = (message = '') => {
                if (!message) {
                    errorMessage.classList.add('hidden');
                    errorMessage.textContent = '';
                    return;
                }
                errorMessage.textContent = message;
                errorMessage.classList.remove('hidden');
            };

            const stopStream = () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            };

            const startStream = async () => {
                stopStream();
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false
                    });
                    video.srcObject = stream;
                    video.classList.remove('hidden');
                    canvas.classList.add('hidden');
                    captureButton.classList.remove('hidden');
                    retakeButton.classList.add('hidden');
                    saveButton.classList.add('hidden');
                    setError('');
                } catch (error) {
                    setError('Unable to access webcam. Please allow camera permissions and retry.');
                }
            };

            openButton.addEventListener('click', async () => {
                webcamCard.classList.remove('hidden');
                await startStream();
            });

            captureButton.addEventListener('click', () => {
                if (!video.videoWidth || !video.videoHeight) {
                    setError('Webcam is not ready yet.');
                    return;
                }

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                stopStream();
                video.classList.add('hidden');
                canvas.classList.remove('hidden');
                captureButton.classList.add('hidden');
                retakeButton.classList.remove('hidden');
                saveButton.classList.remove('hidden');
            });

            retakeButton.addEventListener('click', async () => {
                await startStream();
            });

            saveButton.addEventListener('click', async () => {
                try {
                    saveButton.disabled = true;
                    const image = canvas.toDataURL('image/jpeg', 0.9);
                    const response = await fetch("{{ route('userImages.capture', ['user' => $user->id]) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': "{{ csrf_token() }}",
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ image })
                    });

                    const payload = await response.json();
                    if (!response.ok) {
                        throw new Error(payload.message || 'Could not save image.');
                    }

                    window.location.reload();
                } catch (error) {
                    setError(error.message || 'Could not save image.');
                } finally {
                    saveButton.disabled = false;
                }
            });

            window.addEventListener('beforeunload', () => {
                stopStream();
            });
        })();
    </script>
@endsection
