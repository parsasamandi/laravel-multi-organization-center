<!DOCTYPE html>
<html lang="en">

<!-- Head -->
@section('head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="اتوماسیون مالی، اطلاعات مالی">
    <meta name="keywords" content="گلستان، داشبورد، پنل">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title')</title>
    {{-- App --}}
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
@show

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
                </li>
            </ul>

            {{-- Logout --}}
            <form method="POST" class="form-inline ml-3" action="{{ route('logout', [], false) }}">
                @csrf
                <div class="input-group input-group-sm">

                    <div class="input-group-append">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle text-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                {{-- Admin --}}
                                <input type="hidden" name="admin" />
                                {{-- Exit --}}
                                <button class="dropdown-item text-danger" type="submit"><i class="fa fa-sign-out"></i> خروج</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </nav>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
             <!-- Brand Logo -->
            <a href="/" class="brand-link text-center">
                <object data="/images/golestan-logo-light.svg" class="brand-image img-circle elevation-3" type="image/svg+xml"></object>

                @auth
                    <span class="brand-text font-weight-light">{{ Auth::user()->name }}</span>
                @endauth
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <div>
                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        {{-- Access management --}}
                        @if(Auth::user()->type == 1)
                            <x-admin.urlAddressParent text="مدیریت دسترسی" fontAwesome="fa fa-user">
                                <x-slot name="content">
                                    {{-- General Info --}}
                                    <x-admin.urlAddress text="مراکز" fontAwesome="null" route="{{ url('center/list') }}" />
                                    {{-- Reports --}}
                                    <x-admin.urlAddress text="تیم گلستان" fontAwesome="null" route="{{ url('golestanTeam/list') }}"  />
                                </x-slot>
                            </x-admin.urlAddressParent>
                            {{-- Payment Info --}}
                            <x-admin.urlAddress text="اطلاعات پرداخت‌ها" fontAwesome="fa fa-info" route="{{ url('paymentTransfer/list') }}" />
                            {{-- Center Status Report --}}
                            <x-admin.urlAddress text="وضعیت ماهانه مراکز" fontAwesome="fa fa-check" route="{{ url('centerStatusReport/list') }}" />
                        @endif
                            <x-admin.urlAddressParent text="گزارش مالی ماهانه" fontAwesome="fa fa-file">
                                <x-slot name="content">
                                    {{-- General Info --}}
                                    <x-admin.urlAddress text="صورتحساب بانکی" fontAwesome="null" route="{{ url('generalInfo/list') }}" />
                                    {{-- Reports --}}
                                    <x-admin.urlAddress text="گزارش هزینه‌ها" fontAwesome="null" route="{{ url('report/list') }}"  />
                                </x-slot>
                            </x-admin.urlAddressParent>
                        </ul>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="content">
                <!-- Main content -->
                @yield('content')
            </div>
        </div>

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- Copyright -->
            <div class="float-right d-none d-sm-inline">
                <p>Ⓒ حقوق معنوی سامانه متعلق به خیریه گلستان می‌باشد | </p>
            </div>
            <!-- Responsive Copyright -->
            <div class="responsive-footer"> 
                <span>قالب وبسایت: <a href="https://github.com/badranawad/adminlte-rtl" target="_blank">بدران عوض.</a> </span>
            <div>
        </footer>

    </div>

    <!-- SCRIPTS -->
    @section('scripts')
        {{-- App Js --}}
        <script src="{{ mix('js/manifest.js') }}"></script>
        <script src="{{ mix('js/vendor.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>
        {{-- Ajax request handlers --}}
        <script src="{{ mix('js/RequestHandler.js') }}"></script>
        {{-- Utilities --}}
        <script src="{{ mix('js/utils.js') }}"></script>

        <!-- DataTables JavaScript -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

        <!-- DataTables Buttons JavaScript -->
        <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.0/xlsx.full.min.js"></script>

        <!-- Include Moment.js -->
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

        <!-- Include Moment Jalaali for Jalali date support -->
        <script src="https://cdn.jsdelivr.net/npm/moment-jalaali@0.9.1/moment-jalaali.min.js"></script>


        <script>
            // Ajax Setup
            $.ajaxSetup({ cache: false, processing: true, dataType: "json" });
            // Select2
            $('select').select2({ width:'100%',});
            // Record modal
            $('#return_button').click(function () {
                window.history.back();
            });
        </script>
    @show

</body>
