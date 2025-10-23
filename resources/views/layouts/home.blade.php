<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('vocab.welcome') }}</h1>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <img src="{{ asset('assets/images/app_logo.png') }}" alt="App Logo" class="mx-auto h-48 w-48">
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">{{ __('vocab.about_app') }}</h2>
        <p class="mb-4">{{ __('vocab.app_description') }}</p>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">{{ __('vocab.how_to_login') }}</h3>
            <ul class="list-disc pl-6">
                <li>{{ __('vocab.login_step1') }}</li>
                <li>{{ __('vocab.login_step2') }}</li>
                <li>{{ __('vocab.login_step3') }}</li>
                <li>{{ __('vocab.login_step4') }}</li>
            </ul>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">{{ __('vocab.features') }}</h3>
            <ul class="list-disc pl-6">
                <li>{{ __('vocab.feature1') }}</li>
                <li>{{ __('vocab.feature2') }}</li>
                <li>{{ __('vocab.feature3') }}</li>
                <li>{{ __('vocab.feature4') }}</li>
                <li>{{ __('vocab.feature5') }}</li>
            </ul>
        </div>

        <div class="mt-8">
            <a href="{{ route('login') }}" class="bg-blue-500 text-white btn-app hover:bg-blue-600 px-4 py-2 rounded">
                {{ __('vocab.login') }}
            </a>
        </div>
    </div>

    <div class="mt-4 text-sm">
        <p>{{ __('vocab.need_help') }} <a href="{{ route('messages.contactForm') }}" class="text-green  hover:underline">{{ __('vocab.contact_support') }}</a></p>
    </div>
</div>