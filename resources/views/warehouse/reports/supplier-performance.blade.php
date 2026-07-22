@extends('layouts.warehouse')
@section('title', 'عملکرد تأمین‌کنندگان')

@push('styles')
<style>.chart-container { position: relative; height: 260px; }</style>
@endpush

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center mb-4 gap-3">
    <h4 class="mb-0 fw-bold"><i class="fas fa-truck me-2 text-warning"></i>گزارش عملکرد تأمین‌کنندگان</h4>
  </div>

  {{-- فیلتر --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('warehouse.reports.supplier-performance') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label small fw-semibold">از تاریخ</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold">تا تاریخ</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">اعمال فیلتر</button>
            <a href="{{ route('warehouse.reports.supplier-performance') }}" class="btn btn-outline-secondary"><i class="fas fa-redo"></i></a>
          </div>
          <div class="col-md-3">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-outline-success w-100">
              <i class="fas fa-file-excel me-1"></i> خروجی Excel
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- KPI --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-primary">{{ $rows->count() }}</div>
          <div class="small text-muted">تأمین‌کننده فعال</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold">{{ number_format($rows->sum('total_orders')) }}</div>
          <div class="small text-muted">کل سفارشات</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-success">{{ number_format($rows->sum('total_value')) }}</div>
          <div class="small text-muted">ارزش کل خرید (ریال)</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm border-danger">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-danger">{{ number_format($unpaidBySupplier->sum('unpaid')) }}</div>
          <div class="small text-muted">مانده پرداخت (ریال)</div>
        </div>
      </div>
    </div>
  </div>

  {{-- نمودار نرخ تکمیل --}}
  @if($rows->count())
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent fw-semibold">نرخ تکمیل سفارشات به تفکیک تأمین‌کننده</div>
    <div class="card-body"><div class="chart-container"><canvas id="supplierChart"></canvas></div></div>
  </div>
  @endif

  {{-- جدول --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
      <span class="fw-semibold">جزئیات عملکرد — {{ $dateFrom }} تا {{ $dateTo }}</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>تأمین‌کننده</th>
              <th class="text-center">کل سفارشات</th>
              <th class="text-center">تکمیل‌شده</th>
              <th class="text-center">لغوشده</th>
              <th class="text-center">نرخ تکمیل</th>
              <th class="text-end">ارزش خرید (ریال)</th>
              <th class="text-end text-danger">مانده پرداخت</th>
              <th class="text-center">میانگین تأخیر</th>
              <th class="text-center">وضعیت کلی</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $i => $row)
            @php
              $unpaid    = $unpaidBySupplier[$row->supplier_id]->unpaid ?? 0;
              $rateColor = $row->completion_rate >= 80 ? 'success' : ($row->completion_rate >= 50 ? 'warning' : 'danger');
              $delay     = $row->avg_delay_days;
              $overallScore = ($row->completion_rate >= 80 && ($delay === null || $delay <= 2) && $unpaid == 0)
                              ? 'A' : (($row->completion_rate >= 50) ? 'B' : 'C');
              $scoreColor = match($overallScore) { 'A' => 'success', 'B' => 'warning', 'C' => 'danger' };
            @endphp
            <tr>
              <td class="text-muted small">{{ $i + 1 }}</td>
              <td>
                <div class="fw-medium">{{ $row->supplier_name }}</div>
                @if($row->mobile)<small class="text-muted">{{ $row->mobile }}</small>@endif
              </td>
              <td class="text-center fw-medium">{{ $row->total_orders }}</td>
              <td class="text-center text-success">{{ $row->completed_orders }}</td>
              <td class="text-center text-danger">{{ $row->cancelled_orders }}</td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-2">
                  <div class="progress" style="width:55px;height:6px">
                    <div class="progress-bar bg-{{ $rateColor }}" style="width:{{ $row->completion_rate }}%"></div>
                  </div>
                  <span class="small fw-bold text-{{ $rateColor }}">{{ $row->completion_rate }}%</span>
                </div>
              </td>
              <td class="text-end fw-medium">{{ number_format($row->total_value) }}</td>
              <td class="text-end {{ $unpaid > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                {{ $unpaid > 0 ? number_format($unpaid) : '—' }}
              </td>
              <td class="text-center">
                @if($delay !== null)
                  <span class="badge bg-{{ $delay > 3 ? 'danger' : ($delay > 0 ? 'warning' : 'success') }}-subtle text-{{ $delay > 3 ? 'danger' : ($delay > 0 ? 'warning' : 'success') }}">
                    {{ $delay > 0 ? round($delay).' روز تأخیر' : 'به‌موقع' }}
                  </span>
                @else
                  <small class="text-muted">—</small>
                @endif
              </td>
              <td class="text-center">
                <span class="badge bg-{{ $scoreColor }}-subtle text-{{ $scoreColor }} fs-6 px-3">{{ $overallScore }}</span>
              </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center text-muted py-5">داده‌ای یافت نشد.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- راهنمای درجه‌بندی --}}
  <div class="card border-0 shadow-sm mt-4">
    <div class="card-body py-2">
      <small class="text-muted">
        <strong>راهنمای درجه‌بندی تأمین‌کننده:</strong>
        <span class="badge bg-success-subtle text-success ms-2">A</span> نرخ تکمیل ≥۸۰٪، تأخیر ≤۲ روز، بدون مانده —
        <span class="badge bg-warning-subtle text-warning ms-1">B</span> نرخ تکمیل ≥۵۰٪ —
        <span class="badge bg-danger-subtle text-danger ms-1">C</span> نیاز به بررسی
      </small>
    </div>
  </div>

</div>
@endsection

@push('scripts')
@if($rows->count())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const supplierData = @json($rows->take(10)->values());
new Chart(document.getElementById('supplierChart'), {
  type: 'bar',
  data: {
    labels: supplierData.map(r => r.supplier_name),
    datasets: [
      {
        label: 'نرخ تکمیل (%)',
        data: supplierData.map(r => r.completion_rate),
        backgroundColor: supplierData.map(r =>
          r.completion_rate >= 80 ? 'rgba(113,221,55,.7)' :
          r.completion_rate >= 50 ? 'rgba(255,171,0,.7)' : 'rgba(255,62,29,.6)'
        ),
        borderRadius: 4, yAxisID: 'yPct'
      },
      {
        label: 'ارزش کل (ریال)',
        data: supplierData.map(r => r.total_value),
        type: 'line',
        borderColor: 'rgba(105,108,255,.8)',
        backgroundColor: 'transparent',
        tension: .3, pointRadius: 4, yAxisID: 'yVal'
      }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { position: 'top' } },
    scales: {
      yPct: { type: 'linear', position: 'right', min: 0, max: 100, title: { display: true, text: 'نرخ تکمیل (%)' } },
      yVal: { type: 'linear', position: 'left',  title: { display: true, text: 'ارزش (ریال)' } }
    }
  }
});
</script>
@endif
@endpush

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
