<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('vocab.welcome') }}</h1>
    <div class="max-w-3xl mx-auto">
        @if ($schoolConfig)
        <div class="mb-6">
            <img class="img-circle elevation-2 h-48 w-48 rounded-full mx-auto" src="{{ $schoolConfig->logo_path ?? asset('assets/images/app_logo.png') }}" alt="App Logo" >
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">{{ $schoolConfig->schoolName() }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    @if ($schoolConfig->code)
                    <p class="d-flex gap-2 {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}"><strong>{{ __('vocab.code') }}</strong> {{ $schoolConfig->code }}</p>
                    @endif
                    @if ($schoolConfig->director_name)
                    <p class="d-flex gap-2 {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}"><strong>{{ __('vocab.director_name') }}</strong> {{ $schoolConfig->director_name }}</p>
                    @endif
                    <p class="d-flex gap-2 {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}"><strong>{{ __('vocab.address') }}</strong> {{ $schoolConfig->address }}, {{ $schoolConfig->city }}</p>
                </div>
                <div>
                    <p class="d-flex gap-2 {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}"><strong>{{ __('vocab.phone_number') }}</strong> {{ $schoolConfig->phone_number }}</p>
                    <p class="d-flex gap-2 {{ App::getLocale() == 'ar' ? 'flex-row-reverse' : 'flex-row' }}"><strong>{{ __('vocab.email') }} </strong> {{ $schoolConfig->email }}</p>
                </div>
            </div>
        </div>
        @endif
 
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">{{ __('vocab.about_app') }}</h2>
        <p class="mb-4">{{ __('vocab.app_description') }}</p>

        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">{{ __('vocab.how_to_login') }}</h3>
            <ul class="list-disc pl-6 ">
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