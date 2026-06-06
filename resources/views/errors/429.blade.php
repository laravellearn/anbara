<!DOCTYPE html>
<html lang="fa" class="light-style customizer-hide" dir="rtl"
      data-theme="theme-default"
      data-assets-path="/"
      data-template="vertical-menu-template">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <title>سامانه جامع انبارا - خطای درخواست | 429</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.png') }}">


    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/core.css') }}" class="template-customizer-core-css">
    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css">
    <link rel="stylesheet" href="{{ asset('css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/rtl/rtl.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/libs/typeahead-js/typeahead.css') }}">
    <!-- Vendor -->

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('vendor/css/pages/page-auth.css') }}">
    <!-- Helpers -->
    <script src="{{ asset('vendor/js/helpers.js') }}"></script>

</head>

<body>

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">

            <div class="card1">
                <div style="text-align:center;margin:20px 0 5px;">
                    <a class="d-flex align-items-center justify-content-center"
                       href="{{ route('dashboard') }}"
                       style="text-decoration:none;">
                       <img src="/logo-light.png" alt="Anbara Logo" style="max-width: 180px;height:auto;"
                                data-app-light-img="/logo-light.png" data-app-dark-img="/logo-dark.png">
                    </a>
                </div>

                <div class="card-body text-center">

                    <h1 class="fw-bold mb-2">429</h1>

                    <h4 class="mb-2">خطای درخواست زیاد سمت سرور</h4>

                    <p class="text-muted mb-4">
                        درخواست های زیادی سمت سرور ارسال کردید</br>
                        لطفا 1 دقیقه منتظر بمانید....
                    </p>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-primary">بازگشت</a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">صفحه اصلی</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>


    <!-- Main JS -->
    <script src="{{ asset('js/main.js') }}"></script>


</body>
</html>
