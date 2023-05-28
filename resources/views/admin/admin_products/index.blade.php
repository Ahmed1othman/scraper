@extends('layouts.admin.master')
@section('title')
    {{__('admin.products')}}
@endsection

@section('vendor-style-rtl')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors-rtl.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css') }}">
@endsection

@section('page-style-rtl')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/plugins/forms/form-validation.css') }}">
@endsection


@section('vendor-style-ltr')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/rowGroup.bootstrap5.min.css') }}">
@endsection

@section('page-style-ltr')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/plugins/forms/form-validation.css') }}">
@endsection

@section('content')
    <h3>{{__('admin.products list')}}</h3>
    <p class="mb-2">

    </p>
    <!-- table -->
    <x-alerts.alerts type="success" />
    <x-alerts.alerts type="info" />
    <x-alerts.alerts type="danger" />
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>{{__('admin.list of all products')}} - <span class="badge rounded-pill bg-primary">{{$products->count()}}</span></h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('admin.product ID')}}</th>
                            <th>{{__('admin.product name')}}</th>
                            <th>{{__('admin.product link')}}</th>
                            <th>{{__('admin.product users count')}}</th>
                            <th>{{__('admin.product platform')}}</th>
                            <th>{{__('admin.product last updated price')}}</th>
                            <th>{{__('admin.last update')}}</th>
                            <th>{{__('admin.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr id="row_{{$product->id}}">
                                <td>{{$loop->iteration}}</td>
                                <td>{{$product->id}}</td>
                                <td>{{$product->product_name}}</td>
                                <td><a target="_blank" href="{{$product->url}}"><i data-feather="external-link"></i></a>  </td>
                                <td>{{$product->users_count}}</td>
                                <td>
                                    @if($product->platform == 'amazon')
                                        <img style="padding: 0.25rem; height: auto; width: 80px" src="{{asset('app-assets/images/logo/amazon_logo.png')}}">
                                    @else
                                        <img style="padding: 0.25rem; height: auto; width: 80px" src="{{asset('app-assets/images/logo/noon_logo.png')}}">
                                    @endif
                                </td>
                                <td>{{$product->last_price}}</td>
                                <td>{{$product->updated_at->diffForHumans()}}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-danger btn-sm " style="display: inline-block"  data-bs-toggle="modal" data-bs-target="#delete-modal-{{ $product->id }}">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                        <!-- Delete confirmation modal -->
                                        <div class="modal fade modal-danger text-start text-capitalize" id="delete-modal-{{ $product->id }}" tabindex="-1" aria-labelledby="delete-modal-label-{{ $product->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="delete-modal-label-{{ $product->id }}">{{__('admin.delete confirmation')}}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{__('admin.are use sure to delete this product')}}
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('admin-products.destroy', $product->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{__('admin.cancel')}}</button>
                                                        <button type="submit" class="btn btn-danger">{{__('admin.delete')}}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                </div>
                <div class="card-footer colo align-content-center">
                    <div class="d-flex align-self-center mx-0 row m-2 ">
                        <div class="pagination">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>



@endsection


@section('vendor-js')
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/jszip.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.rowGroup.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/cleave/cleave.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/cleave/addons/cleave-phone.us.js') }}"></script>
@endsection

@section('page-js')
    <script src="{{ asset('app-assets/js/scripts/pages/app-user-list.js') }}"></script>
@endsection


