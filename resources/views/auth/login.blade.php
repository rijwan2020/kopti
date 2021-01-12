<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Authenticate User - {{ config('app.name') }}</title>

    <!-- Icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}">

    <!-- Core stylesheets -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/appwork.css') }}" class="theme-settings-appwork-css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/uikit.css') }}">

    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/authentication.css') }}">
</head>
<body>
    <div class="page-loader">
        <div class="bg-primary"></div>
    </div>
    <div class="authentication-wrapper authentication-3">
        <div class="authentication-inner">
            <div class="d-flex col-lg-4 align-items-center bg-white p-5">
                <div class="d-flex col-sm-7 col-md-5 col-lg-12 px-0 px-xl-4 mx-auto">
                    <div class="w-100">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="ui-w-100">
                                <div class="w-100 position-relative" style="padding-bottom: 54%; height: 100px; height: 100px;">
                                    <img src="{{ config('koperasi.logo')==''?asset('storage/logo.png'):asset('storage/'.config('koperasi.logo')) }}" alt="" class="w-100 h-100 position-absolute">
                                </div>
                            </div>
                        </div>
                        <h4 class="text-center font-weight-normal mt-5 mb-0">Login</h4>
                        <form class="my-5" method="POST" action="{{ route('login') }}">
                            @csrf
    
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" autofocus>
                            </div>
                            <div class="form-group" style="position: relative;">
                                <label class="form-label d-flex justify-content-between align-items-end">
                                    <div>Password</div>
                                </label>
                                <input type="password" class="form-control" name="password" id="password">
                                <div style="position: absolute; right: 5px; top: 50%; transform: translate(-50%,0); cursor: pointer;" id="togglePass">
                                    <i class="fa fa-eye" id="icon-pass"></i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="d-none d-lg-flex col-lg-8 align-items-center ui-bg-cover ui-bg-overlay-container p-5" style="background-image: url('assets/img/bg/bg_new.jpg');">
                <div class="ui-bg-overlay bg-dark opacity-50"></div>
                <div class="w-100 text-white px-5">
                    <h1 class="display-3 font-weight-bolder mb-4">{{config('koperasi.nama')}}</h1>
                    <div class="text-large font-weight-light">
                        {{config('koperasi.deskripsi')}}
                    </div>
                </div>
          </div>
        </div>
    </div>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/toggle-pass.js') }}"></script>
</body>
</html>