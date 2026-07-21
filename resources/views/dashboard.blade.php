@extends('layouts.master')
@section('title', 'داشبورد')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ردیف اول: آمار پایه --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium text-muted">کالاها</span>
            <h3 class="mb-0 mt-1">{{ $productsCount }}</h3>
            <a href="{{ route('warehouse.products.index') }}" class="small text-muted">مشاهده همه</a>
          </div>
          <span class="badge bg-label-primary rounded p-2"><i class="bx bx-package bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium text-muted">انبارها</span>
            <h3 class="mb-0 mt-1">{{ $warehousesCount }}</h3>
            <a href="{{ route('warehouse.warehouses.index') }}" class="small text-muted">مشاهده همه</a>
          </div>
          <span class="badge bg-label-warning rounded p-2"><i class="bx bx-buildings bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium text-muted">کاربران</span>
            <h3 class="mb-0 mt-1">{{ $usersCount }}</h3>
            <a href="{{ route('users.index') }}" class="small text-muted">مشاهده همه</a>
          </div>
          <span class="badge bg-label-success rounded p-2"><i class="bx bx-group bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex align-items-start justify-content-between">
          <div>
            <span class="fw-medium text-muted">دسته‌بندی‌ها</span>
            <h3 class="mb-0 mt-1">{{ $categoriesCount }}</h3>
          </div>
          <span class="badge bg-label-info rounded p-2"><i class="bx bx-category bx-sm"></i></span>
        </div>
      </div>
    </div>
  </div>

  {{-- ردیف دوم: هشدارها و مانیتورینگ --}}
  <h6 class="text-uppercase text-muted fw-medium mb-3" style="letter-spacing:.05em">مانیتورینگ و هشدارها</h6>
  <div class="row g-4 mb-4">

    {{-- هشدار موجودی کم --}}
    <div class="col-sm-6 col-xl-2">
      <a href="{{ route('warehouse.reports.below-minimum') }}" class="text-decoration-none">
        <div class="card shadow-none border {{ $belowMinStock > 0 ? 'border-danger' : '' }} h-100" data-poll-card>
          <div class="card-body d-flex align-items-start justify-content-between">
            <div>
              <span class="fw-medium text-muted small">زیر حداقل موجودی</span>
              <h3 id="stat-below-min" class="mb-0 mt-1 {{ $belowMinStock > 0 ? 'text-danger' : 'text-success' }}">{{ $belowMinStock }}</h3>
            </div>
            <span class="badge {{ $belowMinStock > 0 ? 'bg-label-danger' : 'bg-label-success' }} rounded p-2">
              <i class="bx {{ $belowMinStock > 0 ? 'bx-error' : 'bx-check-circle' }} bx-sm"></i>
            </span>
          </div>
        </div>
      </a>
    </div>

    {{-- درخواست‌های خرید در انتظار --}}
    <div class="col-sm-6 col-xl-2">
      <a href="{{ route('warehouse.purchase-requests.index', ['status'=>'submitted']) }}" class="text-decoration-none">
        <div class="card shadow-none border {{ $pendingPR > 0 ? 'border-info' : '' }} h-100" data-poll-card>
          <div class="card-body d-flex align-items-start justify-content-between">
            <div>
              <span class="fw-medium text-muted small">درخواست خرید معلق</span>
              <h3 id="stat-pending-pr" class="mb-0 mt-1 {{ $pendingPR > 0 ? 'text-info' : 'text-muted' }}">{{ $pendingPR }}</h3>
            </div>
            <span class="badge bg-label-info rounded p-2"><i class="bx bx-cart-add bx-sm"></i></span>
          </div>
        </div>
      </a>
    </div>

    {{-- درخواست‌های کالا در انتظار --}}
    <div class="col-sm-6 col-xl-2">
      <a href="{{ route('warehouse.item-requests.index', ['status'=>'submitted']) }}" class="text-decoration-none">
        <div class="card shadow-none border {{ $pendingIR > 0 ? 'border-info' : '' }} h-100" data-poll-card>
          <div class="card-body d-flex align-items-start justify-content-between">
            <div>
              <span class="fw-medium text-muted small">درخواست کالا معلق</span>
              <h3 id="stat-pending-ir" class="mb-0 mt-1 {{ $pendingIR > 0 ? 'text-info' : 'text-muted' }}">{{ $pendingIR }}</h3>
            </div>
            <span class="badge bg-label-info rounded p-2"><i class="bx bx-task bx-sm"></i></span>
          </div>
        </div>
      </a>
    </div>

    {{-- سفارشات خرید باز --}}
    <div class="col-sm-6 col-xl-2">
      <a href="{{ route('warehouse.purchase-orders.index', ['status'=>'confirmed']) }}" class="text-decoration-none">
        <div class="card shadow-none border h-100" data-poll-card>
          <div class="card-body d-flex align-items-start justify-content-between">
            <div>
              <span class="fw-medium text-muted small">سفارشات خرید باز</span>
              <h3 id="stat-open-po" class="mb-0 mt-1 {{ $openPO > 0 ? 'text-warning' : 'text-muted' }}">{{ $openPO }}</h3>
            </div>
            <span class="badge bg-label-warning rounded p-2"><i class="bx bx-file bx-sm"></i></span>
          </div>
        </div>
      </a>
    </div>

    {{-- فاکتورهای پرداخت‌نشده --}}
    <div class="col-sm-6 col-xl-2">
      <a href="{{ route('warehouse.purchase-invoices.index', ['status'=>'registered']) }}" class="text-decoration-none">
        <div class="card shadow-none border {{ $unpaidInvoices > 0 ? 'border-warning' : '' }} h-100" data-poll-card>
          <div class="card-body d-flex align-items-start justify-content-between">
            <div>
              <span class="fw-medium text-muted small">فاکتور پرداخت‌نشده</span>
              <h3 id="stat-unpaid-inv" class="mb-0 mt-1 {{ $unpaidInvoices > 0 ? 'text-warning' : 'text-muted' }}">{{ $unpaidInvoices }}</h3>
              @if($unpaidAmount > 0)
              <small class="text-danger">{{ number_format($unpaidAmount) }} ﷼</small>
              @endif
            </div>
            <span class="badge bg-label-warning rounded p-2"><i class="bx bx-receipt bx-sm"></i></span>
          </div>
        </div>
      </a>
    </div>

    {{-- اسناد انبار در انتظار --}}
    <div class="col-sm-6 col-xl-2">
      <a href="{{ route('warehouse.documents.index', ['status'=>'pending']) }}" class="text-decoration-none">
        <div class="card shadow-none border {{ $pendingDocs > 0 ? 'border-danger' : '' }} h-100" data-poll-card>
          <div class="card-body d-flex align-items-start justify-content-between">
            <div>
              <span class="fw-medium text-muted small">اسناد در انتظار تأیید</span>
              <h3 id="stat-pending-docs" class="mb-0 mt-1 {{ $pendingDocs > 0 ? 'text-danger' : 'text-muted' }}">{{ $pendingDocs }}</h3>
            </div>
            <span class="badge {{ $pendingDocs > 0 ? 'bg-label-danger' : 'bg-label-secondary' }} rounded p-2">
              <i class="bx bx-file-blank bx-sm"></i>
            </span>
          </div>
        </div>
      </a>
    </div>
  </div>

  {{-- نشانگر آخرین بروزرسانی --}}
  <div class="text-end mb-2">
    <small class="text-muted"><i class="bx bx-refresh me-1"></i>آخرین بروزرسانی: <span id="stat-updated-at">—</span></small>
  </div>

  {{-- ردیف سوم: جدول فعالیت‌ها + اسناد اخیر + اشتراک --}}
  <div class="row g-4">
    <div class="col-md-5">
      <div class="card shadow-none border h-100">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="mb-0">اسناد انبار اخیر</h5>
          <a href="{{ route('warehouse.documents.index') }}" class="btn btn-sm btn-outline-primary">همه اسناد</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>سند</th><th>نوع</th><th>وضعیت</th><th>تاریخ</th></tr>
            </thead>
            <tbody>
              @forelse($recentDocuments as $doc)
              <tr>
                <td>
                  <a href="{{ route('warehouse.documents.show', $doc->id) }}" class="fw-medium text-body">
                    {{ $doc->document_number ?? '#'.$doc->id }}
                  </a>
                </td>
                <td><small>{{ $doc->type }}</small></td>
                <td>
                  @php $sc=['pending'=>['رنگ'=>'warning','متن'=>'معلق'],'approved'=>['رنگ'=>'success','متن'=>'تأیید'],'rejected'=>['رنگ'=>'danger','متن'=>'رد']][$doc->status] ?? ['رنگ'=>'secondary','متن'=>$doc->status]; @endphp
                  <span class="badge bg-label-{{ $sc['رنگ'] }}">{{ $sc['متن'] }}</span>
                </td>
                <td><small class="text-muted">{{ verta($doc->created_at)->format('Y/m/d') }}</small></td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted py-3">سند ثبت‌نشده</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card shadow-none border h-100">
        <div class="card-header border-bottom"><h5 class="mb-0">فعالیت‌های اخیر</h5></div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr><th>زمان</th><th>کاربر</th><th>عملیات</th></tr>
            </thead>
            <tbody>
              @foreach($recentActivities as $log)
              <tr>
                <td><small class="text-muted">{{ verta($log->created_at)->format('Y/m/d H:i') }}</small></td>
                <td><small>{{ $log->user?->name ?? '---' }}</small></td>
                <td><small>{{ Str::limit($log->description ?? $log->action, 40) }}</small></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card shadow-none border">
        <div class="card-body">
          <h6 class="fw-medium mb-3">اشتراک</h6>
          @if($activeSubscription)
            <span class="badge bg-label-success mb-2">{{ $activeSubscription->plan->name }}</span>
            @if($activeSubscription->ends_at)
              @php $remainingDays = \Verta::now()->diffDays(\Verta::instance($activeSubscription->ends_at), false); @endphp
              <p class="mb-0 small {{ $remainingDays <= 7 ? 'text-danger' : 'text-muted' }}">
                {{ $remainingDays > 0 ? $remainingDays.' روز باقی‌مانده' : 'امروز منقضی می‌شود' }}
              </p>
            @else
              <p class="mb-0 small text-success">نامحدود</p>
            @endif
          @else
            <span class="badge bg-label-danger">بدون اشتراک</span>
          @endif
          <a href="{{ route('billing.plans') }}" class="btn btn-sm btn-outline-primary w-100 mt-3">ارتقاء</a>
        </div>
      </div>

      <div class="card shadow-none border mt-3">
        <div class="card-body">
          <h6 class="fw-medium mb-3">دسترسی سریع</h6>
          <div class="d-grid gap-2">
            <a href="{{ route('warehouse.purchase-requests.create') }}" class="btn btn-sm btn-outline-info">
              <i class="bx bx-cart-add me-1"></i> درخواست خرید
            </a>
            <a href="{{ route('warehouse.item-requests.create') }}" class="btn btn-sm btn-outline-info">
              <i class="bx bx-task me-1"></i> درخواست کالا
            </a>
            <a href="{{ route('warehouse.documents.create', ['type'=>'receipt']) }}" class="btn btn-sm btn-outline-success">
              <i class="bx bx-import me-1"></i> رسید انبار
            </a>
            <a href="{{ route('warehouse.reports.below-minimum') }}" class="btn btn-sm btn-outline-danger">
              <i class="bx bx-error me-1"></i> هشدار موجودی
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// ─── Realtime Dashboard Polling (هر ۳۰ ثانیه) ──────────────────────────────
(function() {
  const statsUrl = '{{ route("dashboard.stats") }}';

  // map: json_key → [element-id, optional badge-color-fn]
  const fields = {
    below_min_stock: { id: 'stat-below-min', danger: v => v > 0 },
    pending_pr:      { id: 'stat-pending-pr', danger: v => v > 0 },
    pending_ir:      { id: 'stat-pending-ir', danger: v => v > 0 },
    open_po:         { id: 'stat-open-po',    danger: v => v > 0 },
    unpaid_invoices: { id: 'stat-unpaid-inv', danger: v => v > 0 },
    pending_docs:    { id: 'stat-pending-docs', danger: v => v > 0 },
    updated_at:      { id: 'stat-updated-at', danger: () => false },
  };

  function updateStats(data) {
    for (const [key, cfg] of Object.entries(fields)) {
      const el = document.getElementById(cfg.id);
      if (!el) continue;
      el.textContent = data[key] ?? '—';
      if (cfg.danger) {
        el.classList.toggle('text-danger', cfg.danger(data[key]));
        el.classList.toggle('text-muted',  !cfg.danger(data[key]) && key !== 'updated_at');
      }
    }
    // پالس بصری
    document.querySelectorAll('[data-poll-card]').forEach(card => {
      card.classList.add('border-primary');
      setTimeout(() => card.classList.remove('border-primary'), 800);
    });
  }

  function fetchStats() {
    fetch(statsUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.ok ? r.json() : null)
      .then(data => { if (data) updateStats(data); })
      .catch(() => {});
  }

  // اجرای اولیه پس از ۵ ثانیه، سپس هر ۳۰ ثانیه
  setTimeout(fetchStats, 5000);
  setInterval(fetchStats, 30000);
})();
</script>
@endpush
@endsection