@extends('layouts.main-layout.master')
{{-- Title --}}
@section('main-title')
     @yield('title')
@endsection


<!--sidebar wrapper -->
@section('main-sidebar')
    @include('layouts.main-layout.sidebar')
@endsection
{{-- nav bar --}}
@section('main-navbar')
    @include('layouts.main-layout.nav-bar')
@endsection

{{-- main content --}}
@section('main-content')
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                @include('layouts.main-layout.breadcrumb')
            </div>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>
@endsection

{{-- footer --}}
@section('main-footer')
    {{-- @yield('footer') --}}
    {{-- <h1>heres a footer</h1> --}}
    @include('layouts.main-layout.footer')
@endsection

