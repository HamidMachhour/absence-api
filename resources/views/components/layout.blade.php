<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <script src="{{ asset('js/app.js') }}"></script>

    <title>{{__('vocab.platform')}} {{__('vocab.absence')}}</title>
</head>

<body>
    <header>
        <nav>
            <div class="bg-gray-200 w-full right-0 flex flex-wrap items-center justify-between p-2 gap-2">

                <div class="relative flex rounded-lg border-1 mr-2">
                    <a href="{{ route('home') }}" class="relative left-0 items-center  ">
                        <img src="{{ asset('images/logo.png') }}" class="h-8" alt="Manech logo" />
                    </a>
                    <form action="{{ url()->current() }}" class="relative left-3">
                        <input type="text" name="search"
                            class="h-8  w-full pr-20 rounded-lg border-1 focus:outline-none"
                            placeholder="...بحث" />
                        <div class="absolute top-1 right-2">
                            <button type="submit"
                                class=" w-10 border-1 border-gray-200 rounded-lg border-1 hover:bg-orange-200">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <button data-collapse-toggle="navbar-default" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg border-1 md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-default" aria-expanded="false">
                    <span class="sr-only"></span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>

                <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                    <ul
                        class="font-medium flex flex-col  border border-gray-100 rounded-lg border-1 bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">

                        <li>
                            <a href="{{ route('classes.index') }}"
                                class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                <i class="fa-solid fa-cubes"></i>
                                {{__('vocab.classes')}}</a>
                        </li>
                        @auth
                            <li>
                                <a href="{{ route('seances.manage') }}"
                                    class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                    <i class="fa-solid fa-clock"></i>
                                    {{__('vocab.time_quotas')}} </a>
                            </li>
                            @if (auth()->user()->hasRole('admin') ||
                                    auth()->user()->hasRole('manager'))
                                <li>
                                    <a href="{{ route('classes.manage') }}"
                                        class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                        <i class="fa-solid fa-heart"></i>
                                       {{__('vocab.my_classes')}}</a>
                                </li>
                                <li>
                                    <a href="{{ route('enrollments.index') }}"
                                        class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                        <i class="fa-solid fa-hand"></i>
                                        {{__('vocab.enrollments')}}
                                        <sup class="text-red-500">{{ auth()->user()->nbMyEnrollments() }} </sup>
                                    </a>
                                </li>
                            @elseif (auth()->user()->hasRole('teacher'))
                                <li>
                                    <a href="{{ route('attendances.show', ['attendance' => auth()->user()->attendance()]) }}"
                                        class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                        <i class="fa-solid fa-hand"></i>
                                        {{__('vocab.absences')}}
                                        <sup class="text-red-500">{{ count(auth()->user()->attendance()->attendanceStudents) ?? '0' }}
                                        </sup>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('users.show', ['user' => auth()->user()]) }}"
                                    class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                    <i class="fa-solid fa-user-circle"></i>
                                    {{ auth()->user()->fullName() }}
                                </a>
                            </li>
                            <li>
                                <form class="inline" method="POST"
                                    action="{{ route('users.logout', ['user' => auth()->user()->id]) }}">
                                    @csrf
                                    <button type="submit" class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                        <i class="fa-solid fa-door-open "></i> {{__('vocab.logout')}}
                                    </button>
                                </form>
                            </li>
                        @else
                            <li>
                                <a aria-current="page" href="{{ route('users.register') }}"
                                    class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 ">
                                    <i class="fa-solid fa-user-plus"></i>
                                    {{__('vocab.register')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('users.login') }}"
                                    class="block m-1 p-1 rounded-lg border-1  hover:bg-orange-200 "><i
                                        class="fa-solid fa-arrow-right-to-bracket"></i>
                                    {{__('vocab.login')}}</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class='p-4'>

        {{ $slot }}


    </main>

    <x-flash-message />
</body>

</html>
