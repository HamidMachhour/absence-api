<!DOCTYPE html>
<html lang="en">

@include('layouts.header')

<body>
    <div class="wrapper">
        <!-- Preloader -->
        @yield('preload')
        @include('layouts.headerbar')
        <div class="content-wrapper {{ App::getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
            <!-- Content Header (Page header) -->
            @include('layouts.contentheader')
            <!-- /.content-header -->

            <!-- Main content -->
            @yield('content')
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

    </div>

    <x-flash-message />
    
    @include('layouts.sidebar')
    @include('layouts.footerbar')
    @include('layouts.footer')
</body>

</html>