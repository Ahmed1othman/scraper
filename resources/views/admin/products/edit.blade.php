@extends('layouts.admin.master')
@section('title')
    {{__('admin.products')}}
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
        <x-alerts.alerts type="success" />
        <x-alerts.alerts type="info" />
        <x-alerts.alerts type="danger" />
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{__('admin.edit product')}}</h4>
            </div>
            <div class="card-body">
                <form id="jquery-val-form" method="post" action="{{route('products.update',$product->id)}}">
                    @csrf
                    @method('PUT')
                    <div class="mb-1">
                        <label class="form-label" for="basic-default-name">{{__('admin.product name')}}</label>
                        <input type="text" class="form-control" id="basic-default-name" name="product_name" value="{{$product->product_name}}" placeholder="example: headphone JPL gaming " readonly />
                    </div>


                    <div class="mb-1">
                        <label class="form-label" for="basic-default-name">{{__('admin.product price')}}</label>
                        <input type="number" class="form-control" id="basic-default-name" min="1" name="price" value="{{$userProduct->price}}" placeholder="example: 255.50 " />
                    </div>

                    <div class="mb-1">
                        <label class="form-label" for="select-country">{{__('admin.platform')}}</label>
                        <select class="form-select select2" id="select-country" name="platform" disabled>
                            <option value="">Select Country</option>
                            <option {{$product->platform == 'amazon'?'selected':''}} value="amazon">{{__('admin.amazon')}}</option>
                            <option {{$product->platform == 'noon'?'selected':''}} value="noon">{{__('admin.noon')}}</option>
                        </select>
                    </div>

                    <div class="mb-1">
                        <label class="form-label" for="basic-default-name">{{__('admin.product link')}}</label>
                        <input type="url" class="form-control" id="basic-default-name" name="url" value="{{$product->url}}" placeholder="example: https://www.amazon.com/-/ar/dp/B09TZWLFLY/ref=sr_1_3?keywords=gaming" readonly/>
                    </div>

                    <div class="mb-1">
                        <div class="form-check">
                            <input type="checkbox" name="status" {{$userProduct->status?'checked':''}} class="form-check-input" id="validationCheckBootstrap1" />
                            <label class="form-check-label" for="validationCheckBootstrap1">{{__('admin.product status')}}</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit" value="Submit">{{__('admin.update')}}</button>
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
