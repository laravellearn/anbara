@extends('super-admin.layouts.master')
@section('title', 'جزئیات سازمان - ' . $tenant->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ─── Header ─────────────────────────────────────────────────────────── --}}
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
        <i class="bx bx-arrow-back"></i>
      </a>
      <div class="avatar">
        <span class="avatar-initial rounded bg-label-primary fs-4">{{ mb_substr($tenant->name, 0, 1) }}</span>
      </div>
      <div>
        <h4 class="fw-bold mb-0">{{ $tenant->name }}</h4>
        <code class="text-muted small">{{ $tenant->slug }}</code>
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-warning btn-sm">
        <i class="bx bx-edit me-1"></i> ویرایش
      </a>
      <form action="{{ route('super-admin.tenants.toggle-status', $tenant) }}" method="POST" class="d-inline">
        @csrf
        <button class="btn btn-sm btn-{{ $tenant->is_active ? 'outline-secondary' : 'success' }}">
          <i class="bx bx-{{ $tenant->is_active ? 'pause' : 'play' }} me-1"></i>
          {{ $tenant->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}
        </button>
      </form>
      @php $firstUser = $tenant->users()->first(); @endphp
      @if($firstUser)
      <form action="{{ route('super-admin.impersonate.store') }}" method="POST" class="d-inline">
        @csrf
        <input type="hidden" name="user_id" value="{{ $firstUser->id }}">
        <button class="btn btn-sm btn-primary">
          <i class="bx bx-log-in-circle me-1"></i> ورود به سازمان
        </button>
      </form>
      @endif
    </div>
  </div>

  {{-- ─── KPI Cards ─────────────────────────────────────────────────────── --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-info">{{ $stats['users_count'] }}</div>
        <div class="small text-muted">کاربران</div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-{{ $stats['active_sub'] ? 'success' : 'secondary' }}">
          {{ $stats['active_sub'] ? 'دارد' : 'ندارد' }}
        </div>
        <div class="small text-muted">اشتراک فعال</div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-3 fw-bold text-warning">{{ $stats['total_subs'] }}</div>
        <div class="small text-muted">تعداد اشتراک‌ها</div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <span class="badge fs-6 bg-{{ $tenant->is_active ? 'success' : 'danger' }} py-2">
          {{ $tenant->is_active ? 'فعال' : 'غیرفعال' }}
        </span>
        <div class="small text-muted mt-1">وضعیت</div>
      </div>
    </div>
  </div>

  <div class="row g-4">

    {{-- ─── اطلاعات سازمان ─────────────────────────────────────────────── --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-info-circle text-primary me-2"></i>اطلاعات سازمان</h6>
        </div>
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-5 text-muted">ایمیل</dt>
            <dd class="col-7">{{ $tenant->email ?? '—' }}</dd>
            <dt class="col-5 text-muted">تلفن</dt>
            <dd class="col-7">{{ $tenant->phone ?? '—' }}</dd>
            <dt class="col-5 text-muted">دامنه</dt>
            <dd class="col-7">{{ $tenant->domain ?? '—' }}</dd>
            <dt class="col-5 text-muted">آدرس</dt>
            <dd class="col-7">{{ $tenant->address ?? '—' }}</dd>
            <dt class="col-5 text-muted">تاریخ ثبت</dt>
            <dd class="col-7">{{ $tenant->created_at->toJalali('Y/m/d') }}</dd>
          </dl>
        </div>
      </div>

      {{-- ─── تخصیص پلن ───────────────────────────────────────────────── --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-credit-card text-warning me-2"></i>تخصیص / تغییر پلن</h6>
        </div>
        <div class="card-body">
          @if($stats['active_sub'])
          <div class="alert alert-success py-2 small mb-3">
            پلن فعال: <strong>{{ $stats['active_sub']->plan->name }}</strong>
            @if($stats['active_sub']->ends_at)
              — تا {{ $stats['active_sub']->ends_at->toJalali('Y/m/d') }}
            @else
              — نامحدود
            @endif
          </div>
          @endif
          <form action="{{ route('super-admin.tenants.assign-plan', $tenant) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label small">پلن جدید <span class="text-danger">*</span></label>
              <select name="plan_id" class="form-select form-select-sm" required>
                <option value="">انتخاب کنید</option>
                @foreach($plans as $plan)
                <option value="{{ $plan->id }}">{{ $plan->name }} — {{ number_format($plan->monthly_price) }} تومان</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label small">تاریخ شروع <span class="text-danger">*</span></label>
              <input type="date" name="starts_at" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="form-check mb-3">
              <input type="checkbox" name="cancel_old" value="1" class="form-check-input" id="cancel_old" checked>
              <label class="form-check-label small" for="cancel_old">لغو اشتراک فعلی</label>
            </div>
            <button type="submit" class="btn btn-sm btn-warning w-100">تخصیص پلن</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-7">

      {{-- ─── کاربران ─────────────────────────────────────────────────── --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header d-flex align-items-center justify-content-between py-3">
          <h6 class="mb-0"><i class="bx bx-group text-info me-2"></i>کاربران ({{ $stats['users_count'] }})</h6>
          <a href="{{ route('super-admin.users.index', ['tenant_id' => $tenant->id]) }}" class="btn btn-sm btn-outline-info">
            مشاهده همه
          </a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>نام</th><th>ایمیل</th><th>وضعیت</th><th></th></tr>
            </thead>
            <tbody>
              @forelse($tenant->users as $user)
              <tr>
                <td class="small">{{ $user->name }}</td>
                <td class="small text-muted">{{ $user->email }}</td>
                <td>
                  <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} small">
                    {{ $user->is_active ? 'فعال' : 'مسدود' }}
                  </span>
                </td>
                <td>
                  <form action="{{ route('super-admin.impersonate.store') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <button class="btn btn-xs btn-outline-primary btn-sm py-0 px-1" title="ورود به‌عنوان کاربر">
                      <i class="bx bx-log-in-circle"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-3 small">کاربری ثبت نشده</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- ─── تاریخچه اشتراک‌ها ────────────────────────────────────────── --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-history text-warning me-2"></i>تاریخچه اشتراک‌ها</h6>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr><th>پلن</th><th>شروع</th><th>پایان</th><th>وضعیت</th></tr>
            </thead>
            <tbody>
              @forelse($subscriptions as $sub)
              <tr>
                <td class="small">{{ $sub->plan?->name ?? '—' }}</td>
                <td class="small text-muted">{{ $sub->starts_at?->toJalali('Y/m/d') ?? '—' }}</td>
                <td class="small text-muted">{{ $sub->ends_at?->toJalali('Y/m/d') ?? 'نامحدود' }}</td>
                <td>
                  <span class="badge bg-{{ $sub->status == 'active' ? 'success' : ($sub->status == 'canceled' ? 'danger' : 'secondary') }}">
                    {{ ['active' => 'فعال', 'canceled' => 'لغو', 'expired' => 'منقضی'][$sub->status] ?? $sub->status }}
                  </span>
                </td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-3 small">اشتراکی ثبت نشده</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- ─── فعالیت‌های اخیر ────────────────────────────────────────────── --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-list-ul text-secondary me-2"></i>آخرین فعالیت‌ها</h6>
        </div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
              <tr><th>کاربر</th><th>عملیات</th><th>تاریخ</th></tr>
            </thead>
            <tbody>
              @forelse($logs as $log)
              <tr>
                <td class="small">{{ $log->user?->name ?? 'سیستم' }}</td>
                <td><span class="badge bg-label-secondary small">{{ $log->action }}</span></td>
                <td class="small text-muted">{{ $log->created_at->toJalali('Y/m/d H:i') }}</td>
              </tr>
              @empty
              <tr><td colspan="3" class="text-center text-muted py-3 small">فعالیتی ثبت نشده</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
