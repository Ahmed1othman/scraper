<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- <meta charset="utf-8" /> --}}
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <title>@yield('main-title')</title>
    @include('layouts.main-layout.header')
</head>

    <body class="vertical-layout vertical-menu-modern  navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="">
                <!--sidebar wrapper -->
                @yield('main-sidebar')
                <!--end sidebar wrapper -->
                <!--start header -->
                @yield('main-navbar')
                <!--end header -->
                <!--start page wrapper -->
                @yield('main-content')

{{-- </div> --}}
</body>
<footer>
    @yield('main-footer')
</html>
