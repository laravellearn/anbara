@extends('layouts.master')
@section('title', 'عملکرد تامین‌کنندگان')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- فیلتر --}}
  <div class="card shadow-none border mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('warehouse.reports.supplier-performance') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-medium">از تاریخ</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-medium">تا تاریخ</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> اعمال</button>
            <a href="{{ route('warehouse.reports.supplier-performance') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
          </div>
          <div class="col-md-3">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success w-100">
              <i class="bx bx-download me-1"></i> خروجی Excel
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- KPI --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">تعداد تامین‌کنندگان فعال</span><h3 class="mb-0 mt-1">{{ $rows->count() }}</h3></div>
          <span class="badge bg-label-primary rounded p-2"><i class="bx bx-user-check bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">جمع کل سفارشات</span><h3 class="mb-0 mt-1">{{ $rows->sum('total_orders') }}</h3></div>
          <span class="badge bg-label-info rounded p-2"><i class="bx bx-file bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">جمع ارزش خرید</span><h3 class="mb-0 mt-1 text-primary" style="font-size:1.1rem">{{ number_format($rows->sum('total_value')) }} ﷼</h3></div>
          <span class="badge bg-label-success rounded p-2"><i class="bx bx-dollar-circle bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">مانده پرداخت (کل)</span><h3 class="mb-0 mt-1 text-danger" style="font-size:1.1rem">{{ number_format($unpaidBySupplier->sum('unpaid')) }} ﷼</h3></div>
          <span class="badge bg-label-danger rounded p-2"><i class="bx bx-credit-card bx-sm"></i></span>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-none border">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0"><i class="bx bx-trending-up me-1"></i> عملکرد تامین‌کنندگان
        <small class="text-muted ms-2">{{ $dateFrom }} تا {{ $dateTo }}</small>
      </h5>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>تامین‌کننده</th>
            <th>موبایل</th>
            <th class="text-center">تعداد سفارش</th>
            <th class="text-center">تکمیل‌شده</th>
            <th class="text-center">لغو‌شده</th>
            <th class="text-center">نرخ تکمیل</th>
            <th class="text-end">ارزش کل (ریال)</th>
            <th class="text-end text-danger">مانده پرداخت</th>
            <th class="text-center">میانگین تأخیر</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $i => $row)
          @php
            $unpaid = $unpaidBySupplier[$row->supplier_id]->unpaid ?? 0;
            $rateColor = $row->completion_rate >= 80 ? 'success' : ($row->completion_rate >= 50 ? 'warning' : 'danger');
          @endphp
          <tr>
            <td><small class="text-muted">{{ $i + 1 }}</small></td>
            <td class="fw-medium">{{ $row->supplier_name }}</td>
            <td><small class="text-muted">{{ $row->mobile ?? '—' }}</small></td>
            <td class="text-center">{{ $row->total_orders }}</td>
            <td class="text-center text-success">{{ $row->completed_orders }}</td>
            <td class="text-center text-danger">{{ $row->cancelled_orders }}</td>
            <td class="text-center">
              <div class="d-flex align-items-center gap-1 justify-content-center">
                <div class="progress" style="width:60px;height:6px">
                  <div class="progress-bar bg-{{ $rateColor }}" style="width:{{ $row->completion_rate }}%"></div>
                </div>
                <small class="text-{{ $rateColor }} fw-medium">{{ $row->completion_rate }}%</small>
              </div>
            </td>
            <td class="text-end fw-medium">{{ number_format($row->total_value) }}</td>
            <td class="text-end {{ $unpaid > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
              {{ number_format($unpaid) }}
            </td>
            <td class="text-center">
              @if($row->avg_delay_days !== null)
                <span class="badge bg-label-{{ $row->avg_delay_days > 0 ? 'warning' : 'success' }}">
                  {{ $row->avg_delay_days > 0 ? round($row->avg_delay_days).' روز تأخیر' : 'به‌موقع' }}
                </span>
              @else
                <small class="text-muted">—</small>
              @endif
            </td>
            <td>
              <a href="{{ route('contacts.edit', $row->supplier_id) }}"
                 class="btn btn-sm btn-icon btn-outline-secondary" title="مشاهده تامین‌کننده">
                <i class="bx bx-user"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="11" class="text-center text-muted py-5">داده‌ای یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
