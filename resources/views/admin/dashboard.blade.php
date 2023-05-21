@extends('layouts.admin.master')
@section('title')
{{--    {{$title}}--}}
    لوحة التحكم
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">25</h2>
                        <p class="card-text">عدد المنتجات المضافة</p>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather='shopping-cart' class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="fw-bolder mb-0">10</h2>
                        <p class="card-text">إجمالي العملاء</p>
                    </div>
                    <div class="avatar bg-light-success p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="users" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--        <div class="col-lg-3 col-sm-6 col-12">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">--}}
{{--                    <div>--}}
{{--                        <h2 class="fw-bolder mb-0">0.1%</h2>--}}
{{--                        <p class="card-text">إجمالي عدد الريكوستات</p>--}}
{{--                    </div>--}}
{{--                    <div class="avatar bg-light-danger p-50 m-0">--}}
{{--                        <div class="avatar-content">--}}
{{--                            <i data-feather="activity" class="font-medium-5"></i>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-lg-3 col-sm-6 col-12">--}}
{{--            <div class="card">--}}
{{--                <div class="card-header">--}}
{{--                    <div>--}}
{{--                        <h2 class="fw-bolder mb-0">13</h2>--}}
{{--                        <p class="card-text">إجمالي الاخطاء</p>--}}
{{--                    </div>--}}
{{--                    <div class="avatar bg-light-warning p-50 m-0">--}}
{{--                        <div class="avatar-content">--}}
{{--                            <i data-feather="alert-octagon" class="font-medium-5"></i>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
@endsection
