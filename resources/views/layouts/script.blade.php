<script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('vendor/js/menu.js') }}"></script>

<!-- Vendors JS -->
<script src="{{ asset('vendor/libs/apex-charts/apexcharts.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/dashboards-analytics.js') }}"></script>

<!-- SweetAlert2 JS -->
<script src="{{ asset('js/sweetalert2.js') }}"></script>
  {{-- مطمئن شوید فایل موجود است یا از CDN استفاده کنید --}}



@stack('scripts')