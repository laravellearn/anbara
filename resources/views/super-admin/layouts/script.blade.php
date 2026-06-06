<script src="{{ asset('vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('vendor/libs/hammer/hammer.js') }}"></script>

<script src="{{ asset('vendor/libs/typeahead-js/typeahead.js') }}"></script>

<script src="{{ asset('vendor/js/menu.js') }}"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('vendor/libs/apex-charts/apexcharts.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('js/dashboards-analytics.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // نمایش پیغام‌های Flash
        @if(session('swal_success'))
        Swal.fire({
            icon: 'success',
            title: 'موفق',
            text: '{{ session('
            swal_success ') }}',
            confirmButtonText: 'باشه',
            customClass: {
                confirmButton: 'btn btn-success'
            }
        });
        @endif

        @if(session('swal_error'))
        Swal.fire({
            icon: 'error',
            title: 'خطا',
            text: '{{ session('
            swal_error ') }}',
            confirmButtonText: 'متوجه شدم',
            customClass: {
                confirmButton: 'btn btn-danger'
            }
        });
        @endif

        @if(session('swal_warning'))
        Swal.fire({
            icon: 'warning',
            title: 'توجه',
            text: '{{ session('
            swal_warning ') }}',
            confirmButtonText: 'باشه',
            customClass: {
                confirmButton: 'btn btn-warning'
            }
        });
        @endif

        @if(session('swal_info'))
        Swal.fire({
            icon: 'info',
            title: 'اطلاعات',
            text: '{{ session('
            swal_info ') }}',
            confirmButtonText: 'باشه',
            customClass: {
                confirmButton: 'btn btn-info'
            }
        });
        @endif
    });
</script>
@yield('scripts')