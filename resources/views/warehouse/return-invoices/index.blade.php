@extends('layouts.warehouse')

@section('title', 'اسناد برگشت')

@section('content')
<div class="container-fluid">

  {{-- هدر --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0 fw-bold">اسناد برگشت کالا</h4>
      <small class="text-muted">مدیریت برگشت از فروش و برگشت از خرید</small>
    </div>
    @can('access', 'return-invoices.create')
    <a href="{{ route('warehouse.return-invoices.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> سند برگشت جدید
    </a>
    @endcan
  </div>

  {{-- آمار --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
          <div class="small text-muted">کل اسناد</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-info">{{ number_format($stats['sales']) }}</div>
          <div class="small text-muted">برگشت از فروش</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-warning">{{ number_format($stats['purchase']) }}</div>
          <div class="small text-muted">برگشت از خرید</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-success">{{ number_format($stats['confirmed']) }}</div>
          <div class="small text-muted">تأیید شده</div>
        </div>
      </div>
    </div>
  </div>

  {{-- فیلتر --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <select name="type" class="form-select form-select-sm">
            <option value="">همه انواع</option>
            <option value="sales"    {{ request('type')=='sales'    ? 'selected' : '' }}>برگشت از فروش</option>
            <option value="purchase" {{ request('type')=='purchase' ? 'selected' : '' }}>برگشت از خرید</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه وضعیت‌ها</option>
            <option value="draft"     {{ request('status')=='draft'     ? 'selected' : '' }}>پیش‌نویس</option>
            <option value="confirmed" {{ request('status')=='confirmed' ? 'selected' : '' }}>تأیید شده</option>
            <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>لغو شده</option>
          </select>
        </div>
        <div class="col-md-4">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="جستجو در شماره / دلیل..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-primary flex-fill">فیلتر</button>
          <a href="{{ route('warehouse.return-invoices.index') }}" class="btn btn-sm btn-outline-secondary">پاک</a>
        </div>
      </form>
    </div>
  </div>

  {{-- جدول --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>شماره</th>
              <th>نوع</th>
              <th>تاریخ</th>
              <th>طرف حساب</th>
              <th>انبار</th>
              <th>مبلغ کل</th>
              <th>وضعیت</th>
              <th>ثبت‌کننده</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($returns as $item)
            <tr>
              <td><a href="{{ route('warehouse.return-invoices.show', $item) }}" class="fw-medium text-decoration-none">{{ $item->return_number }}</a></td>
              <td>
                @if($item->type === 'sales')
                  <span class="badge bg-info-subtle text-info">برگشت فروش</span>
                @else
                  <span class="badge bg-warning-subtle text-warning">برگشت خرید</span>
                @endif
              </td>
              <td>{{ $item->return_date->format('Y-m-d') }}</td>
              <td>{{ $item->contact?->name ?? '—' }}</td>
              <td>{{ $item->warehouse?->title ?? '—' }}</td>
              <td class="text-end">{{ number_format($item->total_amount) }}</td>
              <td>
                @php
                  $badge = match($item->status) {
                    'draft'     => 'secondary',
                    'confirmed' => 'success',
                    'cancelled' => 'danger',
                    default     => 'secondary'
                  };
                @endphp
                <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}">{{ $item->status_label }}</span>
              </td>
              <td>{{ $item->creator?->name ?? '—' }}</td>
              <td>
                <a href="{{ route('warehouse.return-invoices.show', $item) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye"></i>
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="9" class="text-center text-muted py-5">هیچ سند برگشتی یافت نشد.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($returns->hasPages())
    <div class="card-footer bg-transparent">{{ $returns->withQueryString()->links() }}</div>
    @endif
  </div>

</div>
@endsection
