@extends('super-admin.layouts.master')
@section('title', 'تنظیمات سیستم')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <h4 class="fw-bold mb-4"><i class="bx bx-cog text-primary me-2"></i>تنظیمات سیستم</h4>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  <div class="row g-4">

    {{-- ─── اطلاعات سیستم ─────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-server text-info me-2"></i>اطلاعات سیستم</h6>
        </div>
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-5 text-muted">نام اپلیکیشن</dt>
            <dd class="col-7">{{ $settings['app_name'] }}</dd>
            <dt class="col-5 text-muted">آدرس سایت</dt>
            <dd class="col-7"><a href="{{ $settings['app_url'] }}" target="_blank" class="text-truncate d-block">{{ $settings['app_url'] }}</a></dd>
            <dt class="col-5 text-muted">محیط</dt>
            <dd class="col-7">
              <span class="badge bg-{{ $settings['app_env'] == 'production' ? 'success' : 'warning' }}">
                {{ $settings['app_env'] }}
              </span>
            </dd>
            <dt class="col-5 text-muted">درایور کش</dt>
            <dd class="col-7"><code>{{ $settings['cache_driver'] }}</code></dd>
            <dt class="col-5 text-muted">درایور صف</dt>
            <dd class="col-7"><code>{{ $settings['queue_driver'] }}</code></dd>
            <dt class="col-5 text-muted">پایگاه‌داده</dt>
            <dd class="col-7">
              <code>{{ $settings['db_connection'] }}</code>
              <span class="badge bg-{{ $dbStatus ? 'success' : 'danger' }} ms-2">
                {{ $dbStatus ? 'متصل' : 'قطع' }}
              </span>
            </dd>
            <dt class="col-5 text-muted">ایمیل ارسال از</dt>
            <dd class="col-7">{{ $settings['mail_from_address'] }}</dd>
            <dt class="col-5 text-muted">درایور ایمیل</dt>
            <dd class="col-7"><code>{{ $settings['mail_mailer'] }}</code></dd>
          </dl>
        </div>
      </div>
    </div>

    {{-- ─── ابزارهای سیستم ─────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-wrench text-warning me-2"></i>ابزارهای مدیریتی</h6>
        </div>
        <div class="card-body">
          <div class="d-grid gap-3">
            <form action="{{ route('super-admin.settings.clear-cache') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-outline-warning w-100"
                onclick="return confirm('کش سیستم پاک شود؟')">
                <i class="bx bx-refresh me-2"></i>پاک‌سازی کش سیستم
                <span class="badge bg-warning text-dark ms-1">config + view + cache</span>
              </button>
            </form>
            <form action="{{ route('super-admin.settings.sync-permissions') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-outline-info w-100"
                onclick="return confirm('مجوزها همگام‌سازی شوند؟')">
                <i class="bx bx-sync me-2"></i>همگام‌سازی مجوزها
                <span class="badge bg-info ms-1">PermissionSeeder</span>
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-info-circle text-secondary me-2"></i>اطلاعات محیط PHP</h6>
        </div>
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-5 text-muted">نسخه PHP</dt>
            <dd class="col-7"><code>{{ PHP_VERSION }}</code></dd>
            <dt class="col-5 text-muted">نسخه Laravel</dt>
            <dd class="col-7"><code>{{ app()->version() }}</code></dd>
            <dt class="col-5 text-muted">منطقه زمانی</dt>
            <dd class="col-7"><code>{{ config('app.timezone') }}</code></dd>
            <dt class="col-5 text-muted">حافظه مجاز</dt>
            <dd class="col-7"><code>{{ ini_get('memory_limit') }}</code></dd>
          </dl>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
