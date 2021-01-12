<!DOCTYPE html>

<html lang="en" class="default-style">
    <head>
        <title>{{ config('title', 'Home').' - '.config('app.name') }}</title>

        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1" />
        <meta name="description" content="" />
        <meta name="viewport"  content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <link rel="icon" type="image/x-icon" href="favicon.ico" />

        <!-- Icon fonts -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/ionicons.css') }}" />

        <!-- Core stylesheets -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/bootstrap.css') }}" class="theme-settings-bootstrap-css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/appwork.css') }}" class="theme-settings-appwork-css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-corporate.css') }}" class="theme-settings-theme-css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/colors.css') }}" class="theme-settings-colors-css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/uikit.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

        <!-- Load polyfills -->
        <script src="{{ asset('assets/vendor/js/polyfills.js') }}"></script>

        <script src="{{ asset('assets/vendor/js/material-ripple.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/layout-helpers.js') }}"></script>

        <!-- Core scripts -->
        <script src="{{ asset('assets/vendor/js/pace.js') }}"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <!-- Libs -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}"/>

        @yield('styles')
    </head>

    <body>
        <div class="page-loader">
            <div class="bg-primary"></div>
        </div>
        <div class="layout-wrapper layout-2">
            <div class="layout-inner">
                @include('layouts.nav')

                <div class="layout-container">
                    <nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center bg-white container-p-x" id="layout-navbar">
                        <a href="{{ route('home') }}" class="navbar-brand app-brand demo d-lg-none py-0 mr-4" >
                            <img src="{{ config('koperasi.logo')==''?asset('storage/logo.png'):asset('storage/'.config('koperasi.logo')) }}" alt="" class="app-brand-logo demo">
                            <span class="app-brand-text demo font-weight-normal ml-2">{{config('koperasi.nama')}}</span>
                        </a>
                        <div class="layout-sidenav-toggle navbar-nav d-lg-none align-items-lg-center">
                            <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:void(0)">
                                <i class="fa fa-bars text-large align-middle"></i>
                            </a>
                        </div>
                        <div class="navbar-collapse collapse" id="layout-navbar-collapse">
                            <hr class="d-lg-none w-100 my-2" />
                            <div class="navbar-nav align-items-lg-center">
                                Aplikasi Koperasi - {{config('koperasi.nama')}}
                            </div>

                            <div class="navbar-nav align-items-lg-center ml-auto">
                                <div class="demo-navbar-user nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                        <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                                            <img src="{{ auth()->user()->image !='' ? asset('storage/'.auth()->user()->image) : asset('storage/profile.png') }}" alt class="d-block ui-w-30 rounded-circle"/>
                                            <span class="px-1 mr-lg-2 ml-2 ml-lg-0" >{{auth()->user()->name}}</span>
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('profile') }}" class="dropdown-item">
                                            <i class="fa fa-user text-lightest"></i>
                                            &nbsp; My profile
                                        </a>
                                        <a href="{{ route('logout') }}" class="dropdown-item">
                                            <i class="fa fa-sign-out-alt text-lightest"></i>
                                            &nbsp; Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>
                    <div class="layout-content">
                        <div class="container-fluid flex-grow-1 container-p-y">
                            @include('layouts.breadcrumb')
                            
                            @include('layouts.warning')
                            @yield('content')
                        </div>
                        <nav class="layout-footer footer bg-footer-theme">
                            <div class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3">
                                <div class="pt-3">
                                    Copyright &copy; <a href="https://4visionmedia.com" target="_blank"><b>4 Vision Media</b></a> - 2020
                                </div>
                                <div>
                                    <a href="{{ route('home') }}" class="footer-link pt-3"><b>{{config('koperasi.nama')}}</b></a>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="layout-overlay layout-sidenav-toggle"></div>
        </div>
        <!-- Core scripts -->
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/sidenav.js') }}"></script>

        <!-- Libs -->
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
        <script src="{{ asset('js/jquery.mask.min.js') }}"></script>

        <!-- Demo -->
        <script src="{{ asset('assets/js/demo.js') }}"></script>
        <script src="{{ asset('assets/js/ui_tooltips.js') }}"></script>

        @yield('scripts')
    </body>
</html>
