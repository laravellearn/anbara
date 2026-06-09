<!DOCTYPE html>
<html lang="fa" class="light-style customizer-hide" dir="rtl" data-theme="theme-default" data-assets-path="/"
    data-template="vertical-menu-template">
@include('layouts.head')

</head>

<body>
    {{-- ==================== NAVBAR ==================== --}}
    @include('layouts.navbar')

    {{-- ==================== WRAPPER اصلی (سایدبار + محتوا) ==================== --}}
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            @include('layouts.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper (اسکرول‌شونده) -->
                <div class="content-wrapper">
                    <!-- Content -->
                    @yield('content')
                    <!-- / Content -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content wrapper -->

                <!-- Footer (ثابت در پایین) -->
                @include('layouts.footer')
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    @include('layouts.script')



    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Global Toast Script --}}
    <script>
        window.showToast = function(message, type = 'success', title = '') {
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 loaded!');
                alert(message);
                return;
            }

            const titles = {
                success: '✅ موفق',
                error: '❌ خطا',
                warning: '⚠️ هشدار',
                info: 'ℹ️ اطلاعیه'
            };

            Swal.fire({
                toast: true,
                position: 'bottom', // پایین سمت چپ
                icon: type,
                title: title || titles[type] || 'پیام',
                text: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
                customClass: {
                    popup: 'colored-toast'
                }
            });
        };
        // تابع global برای confirm (جایگزین confirm جاوااسکریپت)
        window.showConfirm = function(title, message, onConfirm, onCancel = null) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'بله',
                cancelButtonText: 'لغو',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed && onConfirm) {
                    onConfirm();
                } else if (onCancel) {
                    onCancel();
                }
            });
        };
    </script>


    {{-- نمایش خودکار toast از session --}}
    @if (session('toast'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = @json(session('toast'));
                window.showToast(toast.message, toast.type, toast.title);
            });
        </script>
    @endif

</body>

</html>
