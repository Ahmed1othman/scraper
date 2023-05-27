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
                <h4 class="card-title">{{__('admin.edit user')}}</h4>
            </div>
            <div class="card-body">
                <form id="jquery-val-form" method="post" action="{{route('users.update',$user->id)}}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" class="form-control" value="{{$user->id}}" id="basic-default-id" name="id" />

                    <div class="row">
                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="basic-default-name">{{__('admin.user name')}}</label>
                            <input type="text" class="form-control" value="{{$user->name}}" id="basic-default-name" name="name" placeholder="example: ali mohamed" />
                        </div>

                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="basic-default-phone">{{__('admin.user phone')}}</label>
                            <input type="test" maxlength="11" minlength="11" value="{{$user->phone}}" class="form-control" id="basic-default-phone" name="phone" placeholder="example: 01011111111" required/>
                        </div>

                        <div class="mb-1 col-md-6">
                            <label class="form-label" for="basic-default-email">{{__('admin.email')}}</label>
                            <input type="email" class="form-control" value="{{$user->email}}" id="basic-default-email" name="email" placeholder="example: example@gmail.com" />
                        </div>

                        <div class="mb-1 col-12">
                            <div class="form-check">
                                <input type="checkbox" name="status" {{$user->status?'checked':''}}  class="form-check-input" id="validationCheckBootstrap" />
                                <label class="form-check-label" for="validationCheckBootstrap">{{__('admin.user status')}}</label>
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
