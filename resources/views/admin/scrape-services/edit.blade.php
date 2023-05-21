@extends('layouts.admin.master')
@section('title')
    {{__('admin.users')}}
@endsection

@section('vendor-style-rtl')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors-rtl.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
@endsection
@section('page-style-rtl')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/plugins/forms/pickers/form-flat-pickr.css')}}">
@endsection
@section('vendor-style-ltr')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
@endsection
@section('page-style-ltr')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/form-validation.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
@endsection


@section('content')
    <!-- jQuery Validation -->
    <div class="col-md-12 col-12">
        <x-alerts.validation-errors :errors="$errors" />
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{__('admin.edit service')}}</h4>
            </div>
            <div class="card-body">
                <form id="jquery-val-form" method="post" action="{{route('scrape-services.update',$serviceConfiguration->id)}}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="basic-default-username">{{__('admin.service username')}}</label>
                            <input type="text" class="form-control" value="{{$serviceConfiguration->username}}" id="basic-default-name" name="username" />
                        </div>
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="basic-default-password">{{__('admin.service password')}}</label>
                            <input type="text" class="form-control" value="{{$serviceConfiguration->password}}" id="basic-default-password" name="password"  />
                        </div>

                        <div class="mb-1 col-12">
                            <div class="form-check">
                                <input type="checkbox" name="status" {{$serviceConfiguration->status?'checked':''}}  class="form-check-input" id="validationCheckBootstrap" />
                                <label class="form-check-label" for="validationCheckBootstrap">{{__('admin.service status')}}</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" name="submit" value="Submit">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <!-- /jQuery Validation -->
@endsection

@section('vendor-js')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js')}}"></script>
@endsection
@section('page-js')
    <script src="{{asset('app-assets/js/scripts/forms/form-validation.js')}}"></script>
@endsection
