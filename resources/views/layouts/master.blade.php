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
</body>
</html>