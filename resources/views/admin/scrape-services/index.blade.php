@extends('layouts.admin.master')
@section('title')
    {{__('admin.users')}}
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
    <h3>{{__('admin.services list')}}</h3>
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
{{--                    <a class="btn btn-primary" href="{{route('scrape-services.create')}}"><i data-feather="plus"></i> {{__('admin.add service')}}</a>--}}
                </div>
                <div class="table-responsive">
                    <table class="table table-responsive" >
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('admin.service username')}}</th>
                            <th>{{__('admin.service password')}}</th>
                            <th>{{__('admin.service status')}}</th>
                            <th>{{__('admin.actions')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($scrapeServices as $scrapeService)
                            <tr id="row_{{$scrapeService->id}}">
                                <td>{{$loop->iteration}}</td>
                                <td>{{$scrapeService->username}}</td>
                                <td>{{$scrapeService->password}}</td>
                                <td>
                                    @if($scrapeService->status)
                                        <span class="badge rounded-pill bg-success">{{__('admin.enabled')}}</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary">{{__('admin.disabled')}}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('scrape-services.edit', $scrapeService->id) }}" style="display: inline-block" class="btn btn-primary mx-1  btn-sm  ">
                                            <i data-feather="edit"></i>
                                        </a>
{{--                                        <button type="button" class="btn btn-danger btn-sm " style="display: inline-block"  data-bs-toggle="modal" data-bs-target="#delete-modal-{{ $scrapeService->id }}">--}}
{{--                                            <i data-feather="trash-2"></i>--}}
{{--                                        </button>--}}
                                        <!-- Delete confirmation modal -->
{{--                                        <div class="modal fade modal-danger text-start text-capitalize" id="delete-modal-{{ $scrapeService->id }}" tabindex="-1" aria-labelledby="delete-modal-label-{{ $scrapeService->id }}" aria-hidden="true">--}}
{{--                                            <div class="modal-dialog modal-dialog-centered">--}}
{{--                                                <div class="modal-content">--}}
{{--                                                    <div class="modal-header">--}}
{{--                                                        <h5 class="modal-title" id="delete-modal-label-{{ $scrapeService->id }}">{{__('admin.delete confirmation')}}</h5>--}}
{{--                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-body">--}}
{{--                                                        {{__('admin.are use sure to delete this service')}}--}}
{{--                                                    </div>--}}
{{--                                                    <div class="modal-footer">--}}
{{--                                                        <form action="{{ route('scrape-services.destroy', $scrapeService->id) }}" method="POST">--}}
{{--                                                            @csrf--}}
{{--                                                            @method('DELETE')--}}
{{--                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{__('admin.cancel')}}</button>--}}
{{--                                                            <button type="submit" class="btn btn-danger">{{__('admin.delete')}}</button>--}}
{{--                                                        </form>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div></div>--}}
                                </td>
                            </tr>

                        @endforeach
                        </tbody>
                    </table>
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


