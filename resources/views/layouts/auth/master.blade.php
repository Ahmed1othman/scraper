@extends('layouts.main-layout.master')
{{-- Title --}}
@section('main-title')
     @yield('title')
@endsection



{{-- main content --}}
@section('main-content')
     @yield('content')

     @include('layouts.main-layout.footer')
@endsection


