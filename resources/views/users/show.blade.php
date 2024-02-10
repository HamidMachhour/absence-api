@extends('layouts.master')

@section('title')
    منصة ثانوية ابن هاني التأهيلية بفاس
@endsection

@section('css')
@endsection

@section('preload')
@endsection
@section('main-page')
    <a href="{{ route('home.route') }}">الرئيسية</a>
@endsection

@section('page-title')
    الرئيسية
@endsection

@section('page')
    المستعمل
@endsection

@section('content')
    <div class="content-wrapper" style="min-height: 2646.44px;">
        <!-- Main content -->
        <section class="content">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <a href="{{ route('userImages.show', ['user' => $user->id]) }}">
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="{{ $user->userImage->uri ?? asset('images/no-image.png') }}"
                                        alt="User profile picture">
                                </a>
                            </div>

                            <h3 class="profile-username text-center">{{ $user->lastName . ' ' . $user->firstName }}</h3>

                            <p class="text-muted text-center">Software Engineer</p>

                            <ul class="list-group list-group-unbordered mb-3">

                                <li class="list-group-item">
                                    <a class="float-right">{{ $user->email }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Followers</b> <a class="float-right">1,322</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Following</b> <a class="float-right">543</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Friends</b> <a class="float-right">13,287</a>
                                </li>
                            </ul>

                            @auth
                                @if (auth()->user()->id === $user->id ||
                                        auth()->user()->hasRole('admin'))
                                    <div class=" mb-2">
                                        <a href="{{ route('users.edit', ['user' => $user->id]) }}"
                                            class="btn btn-primary btn-block">
                                            <i class="fas fa-edit"></i>
                                            تغيير
                                        </a>
                                    </div>
                                    @if (auth()->user()->hasRole('admin'))
                                        <div class=" mb-2">
                                            <form action="{{ route('users.destroy', ['user' => $user->id]) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-block"
                                                    onclick="return confirm('Are you sure you want to delete this account?')">
                                                    <i class="fas fa-trash"></i>
                                                    حدف
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                    <div class=" mb-2">
                                        <form class="inline" method="POST"
                                            action="{{ route('users.logout', ['user' => auth()->user()->id]) }}">
                                            @csrf
                                            <input type="hidden" name="logout_all_devices" value="1">

                                            <button type="submit" class="btn btn-success btn-block">
                                                <i class="fas fa-door-closed "></i> تسجيل الخروج من جميع الاجهزة
                                            </button>
                                        </form>
                                    </div>
                                    <div class=" mb-2">
                                        <a href="mailto:{{ $user->email }}" class="btn btn-block bg-gradient-info">
                                            <i class="fas fa-envelope "></i>
                                            مراسلة</a>
                                    </div>
                                @else
                                @endif
                            @endauth
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
        </section>
        <!-- /.content -->
    </div>
@endsection

@section('scripts')
@endsection
