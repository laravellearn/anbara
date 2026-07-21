@extends('super-admin.layouts.master')
@section('title', 'داشبورد مدیریت کل')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ─── عنوان ─────────────────────────────────────────────────────────── --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      <h4 class="fw-bold mb-1"><i class="bx bx-tachometer text-primary me-2"></i>داشبورد مدیریت کل</h4>
      <p class="text-muted mb-0 small">آخرین به‌روزرسانی: {{ now()->toJalali('Y/m/d H:i') }}</p>
    </div>
    <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">
      <i class="bx bx-plus me-1"></i> سازمان جدید
    </a>
  </div>

  {{-- ─── KPI Cards ─────────────────────────────────────────────────────── --}}
  <div class="row g-4 mb-4">

    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-primary p-3">
              <i class="bx bx-buildings fs-4"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">کل سازمان‌ها</p>
            <h4 class="fw-bold mb-0">{{ number_format($totalTenants) }}</h4>
            <small class="text-success"><i class="bx bx-up-arrow-alt"></i> {{ $newTenantsThisMonth }} این ماه</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-success p-3">
              <i class="bx bx-check-circle fs-4"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">سازمان‌های فعال</p>
            <h4 class="fw-bold mb-0 text-success">{{ number_format($activeTenants) }}</h4>
            <small class="text-danger">{{ $inactiveTenants }} غیرفعال</small>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-warning p-3">
              <i class="bx bx-credit-card fs-4"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">اشتراک‌های فعال</p>
            <h4 class="fw-bold mb-0 text-warning">{{ number_format($totalSubscriptions) }}</h4>
            @if($expiringSoon->count())
            <small class="text-danger"><i class="bx bx-alarm"></i> {{ $expiringSoon->count() }} در حال انقضا</small>
            @else
            <small class="text-muted">همه سالم</small>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-info p-3">
              <i class="bx bx-group fs-4"></i>
            </span>
          </div>
          <div>
            <p class="mb-0 text-muted small">کل کاربران</p>
            <h4 class="fw-bold mb-0 text-info">{{ number_format($totalUsers) }}</h4>
            <small class="text-muted">در همه سازمان‌ها</small>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="row g-4 mb-4">

    {{-- ─── سازمان‌های اخیر ───────────────────────────────────────────────── --}}
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
          <h5 class="mb-0"><i class="bx bx-buildings text-primary me-2"></i>آخرین سازمان‌ها</h5>
          <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-sm btn-outline-primary">مشاهده همه</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>سازمان</th>
                <th>پلن</th>
                <th>وضعیت</th>
                <th>تاریخ ثبت</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentTenants as $tenant)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm">
                      <span class="avatar-initial rounded bg-label-secondary">{{ mb_substr($tenant->name, 0, 1) }}</span>
                    </div>
                    <div>
                      <strong>{{ $tenant->name }}</strong>
                      <div class="text-muted small">{{ $tenant->email ?? $tenant->slug }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  @if($tenant->activeSubscription?->plan)
                    <span class="badge bg-label-primary">{{ $tenant->activeSubscription->plan->name }}</span>
                  @else
                    <span class="badge bg-label-secondary">بدون اشتراک</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-{{ $tenant->is_active ? 'success' : 'danger' }}">
                    {{ $tenant->is_active ? 'فعال' : 'غیرفعال' }}
                  </span>
                </td>
                <td class="text-muted small">{{ $tenant->created_at->toJalali('Y/m/d') }}</td>
                <td>
                  <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-sm btn-icon btn-outline-secondary">
                    <i class="bx bx-show"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted py-4">هیچ سازمانی ثبت نشده</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ─── توزیع پلن + اشتراک در حال انقضا ────────────────────────────── --}}
    <div class="col-xl-4">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-pie-chart text-warning me-2"></i>توزیع پلن‌ها</h6>
        </div>
        <div class="card-body py-2">
          @forelse($planDistribution as $item)
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small">{{ $item->name }}</span>
            <span class="badge bg-label-primary">{{ $item->cnt }} سازمان</span>
          </div>
          @empty
          <p class="text-muted small mb-0">اشتراک فعالی وجود ندارد.</p>
          @endforelse
        </div>
      </div>

      @if($expiringSoon->count())
      <div class="card border-0 shadow-sm border-start border-danger border-3">
        <div class="card-header py-3">
          <h6 class="mb-0 text-danger"><i class="bx bx-alarm me-2"></i>انقضا در ۳۰ روز</h6>
        </div>
        <div class="card-body py-2">
          @foreach($expiringSoon->take(5) as $sub)
          <div class="d-flex justify-content-between align-items-center mb-2 small">
            <span>{{ $sub->tenant?->name }}</span>
            <span class="badge bg-danger">{{ $sub->ends_at->toJalali('Y/m/d') }}</span>
          </div>
          @endforeach
          @if($expiringSoon->count() > 5)
          <a href="{{ route('super-admin.subscriptions.index', ['expiring' => 1]) }}" class="small text-primary">
            + {{ $expiringSoon->count() - 5 }} مورد دیگر
          </a>
          @endif
        </div>
      </div>
      @endif
    </div>

  </div>

  {{-- ─── فعالیت‌های اخیر ─────────────────────────────────────────────────── --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between py-3">
      <h5 class="mb-0"><i class="bx bx-history text-secondary me-2"></i>فعالیت‌های اخیر</h5>
      <a href="{{ route('super-admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">مشاهده همه</a>
    </div>
    <div class="table-responsive">
      <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>کاربر</th><th>سازمان</th><th>عملیات</th><th>تاریخ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentLogs as $log)
          <tr>
            <td class="small">{{ $log->user?->name ?? 'سیستم' }}</td>
            <td class="small text-muted">{{ $log->tenant?->name ?? '—' }}</td>
            <td><span class="badge bg-label-secondary small">{{ $log->action }}</span></td>
            <td class="small text-muted">{{ $log->created_at->toJalali('Y/m/d H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted py-3">فعالیتی ثبت نشده</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
