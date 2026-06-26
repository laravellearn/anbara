<meta charset="utf-8">
<meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

<title>@yield('title') - سامانه جامع انبارا</title>
<meta name="description" content="">

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.png') }}">

<!-- Icons -->
<link rel="stylesheet" href="{{ asset('vendor/fonts/boxicons.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/fonts/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/fonts/flag-icons.css') }}">

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('vendor/css/rtl/core.css') }}" class="template-customizer-core-css">
<link rel="stylesheet" href="{{ asset('vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css">
<link rel="stylesheet" href="{{ asset('css/demo.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/css/rtl/rtl.css') }}">

<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/libs/typeahead-js/typeahead.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/libs/apex-charts/apex-charts.css') }}">

<!-- Page CSS -->

<!-- Helpers -->
<script src="{{ asset('vendor/js/helpers.js') }}"></script>

<script src="{{ asset('vendor/js/template-customizer.js') }}"></script>
<script src="{{ asset('js/config.js') }}"></script>

<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

<link rel="stylesheet" href="{{ asset('/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/libs/bootstrap-select/bootstrap-select.css') }}">


<style>
    :root {
        --navbar-height: 62px;
        /* ارتفاع واقعی navbar را با Inspect اندازه بگیرید */
        --sidebar-width: 250px;
        /* عرض سایدبار */
    }

    /* navbar فیکس؛ فقط محدودهٔ محتوای اصلی را می‌پوشاند */
    .layout-navbar.fixed-top {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1030;
        height: var(--navbar-height);
    }

    body {
        overflow: hidden;
    }

    .layout-wrapper {
        height: calc(100vh - var(--navbar-height));
        display: flex;
    }

    .layout-container {
        display: flex;
        height: 100%;
        width: 100%;
    }

    .layout-menu {
        flex-shrink: 0;
        /* اسکرول سایدبار با استایل اینلاین خودش */
    }

    /* محتوای اصلی: flex column */
    .layout-page {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        /* خود صفحه اسکرول نمی‌شود */
    }

    /* بخش بالایی (محتوا) با قابلیت اسکرول */
    .content-wrapper {
        flex-grow: 1;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    /* اسکرول‌بار webkit */
    .content-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    .content-wrapper::-webkit-scrollbar-thumb {
        background-color: #c1c1c1;
        border-radius: 3px;
    }

    /* فوتر همواره پایین صفحه قرار دارد */
    .layout-footer {
        flex-shrink: 0;
        /* ارتفاع آن کم و زیاد نشود */
        /* می‌توانید height مشخص کنید یا خودکار باشد */
    }

    /* #liveSearch {
        margin-top: -2px;
        transform: translateY(-1px);
    } */

    .accordion-button .badge {
        transition: all 0.2s ease;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .select-group-btn,
    .deselect-group-btn {
        font-size: 0.75rem;
        padding: 0.2rem 0.5rem;
    }

    .permission-checkbox:checked+label {
        color: #696cff;
        font-weight: 500;
    }
</style>

@yield('styles')