@extends('layouts.warehouse')
@section('title', 'داشبورد مدیریتی پیشرفته')

@push('styles')
<style>
.stat-card { transition: transform .2s; }
.stat-card:hover { transform: translateY(-3px); }
.chart-container { position: relative; height: 300px; }
.chart-sm { position: relative; height: 220px; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bx bx-tachometer me-2"></i> داشبورد مدیریتی انبار</h4>
    <span class="text-muted small">آخرین بروزرسانی: {{ now()->format('Y/m/d H:i') }}</span>
  </div>

  {{-- ═══ KPI Cards ═══ --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border stat-card h-100">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted d-block mb-1">کل اقلام کالا</span>
            <h2 class="mb-0">{{ number_format($kpi['total_products']) }}</h2>
            <small class="text-muted">در {{ $kpi['total_warehouses'] }} انبار فعال</small>
          </div>
          <span class="badge bg-label-primary rounded p-2 mt-1"><i class="bx bx-package bx-md"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border stat-card h-100 {{ $kpi['pending_docs'] > 0 ? 'border-warning' : '' }}">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted d-block mb-1">اسناد در انتظار تأیید</span>
            <h2 class="mb-0 {{ $kpi['pending_docs'] > 0 ? 'text-warning' : '' }}">{{ $kpi['pending_docs'] }}</h2>
            <a href="{{ route('warehouse.documents.index', ['status' => 'pending']) }}" class="small">مشاهده اسناد</a>
          </div>
          <span class="badge bg-label-{{ $kpi['pending_docs'] > 0 ? 'warning' : 'secondary' }} rounded p-2 mt-1"><i class="bx bx-file bx-md"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border stat-card h-100 {{ $kpi['open_po'] > 0 ? 'border-info' : '' }}">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted d-block mb-1">سفارشات خرید باز</span>
            <h2 class="mb-0 {{ $kpi['open_po'] > 0 ? 'text-info' : '' }}">{{ $kpi['open_po'] }}</h2>
            <a href="{{ route('warehouse.purchase-orders.index', ['status' => 'confirmed']) }}" class="small">مشاهده PO‌ها</a>
          </div>
          <span class="badge bg-label-info rounded p-2 mt-1"><i class="bx bx-cart bx-md"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border stat-card h-100 {{ $kpi['below_min_count'] > 0 ? 'border-danger' : '' }}">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted d-block mb-1">کالای زیر حداقل</span>
            <h2 class="mb-0 {{ $kpi['below_min_count'] > 0 ? 'text-danger' : '' }}">{{ $kpi['below_min_count'] }}</h2>
            <a href="{{ route('warehouse.reports.below-minimum') }}" class="small">مشاهده لیست</a>
          </div>
          <span class="badge bg-label-danger rounded p-2 mt-1"><i class="bx bx-error bx-md"></i></span>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══ KPI Row 2: مالی ═══ --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
      <div class="card shadow-none border stat-card h-100 {{ $invoiceKpi['sales_unpaid'] > 0 ? 'border-warning' : '' }}">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted d-block mb-1">مطالبات وصول‌نشده</span>
            <h2 class="mb-0 text-warning">{{ number_format($invoiceKpi['sales_unpaid']) }}</h2>
            <small class="text-muted">ریال — فاکتورهای فروش</small>
          </div>
          <span class="badge bg-label-warning rounded p-2 mt-1"><i class="bx bx-money bx-md"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4">
      <div class="card shadow-none border stat-card h-100">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted d-block mb-1">فاکتور خرید پرداخت‌نشده</span>
            <h2 class="mb-0">{{ number_format($invoiceKpi['purchase_unpaid']) }}</h2>
            <small class="text-muted">تعداد فاکتور</small>
          </div>
          <span class="badge bg-label-danger rounded p-2 mt-1"><i class="bx bx-receipt bx-md"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4">
      <div class="card shadow-none border stat-card h-100">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted d-block mb-1">برگشتی این ماه</span>
            <h2 class="mb-0">{{ number_format($invoiceKpi['returns_this_month']) }}</h2>
            <small class="text-muted">سند برگشت فروش/خرید</small>
          </div>
          <span class="badge bg-label-secondary rounded p-2 mt-1"><i class="bx bx-undo bx-md"></i></span>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══ Charts Row 1 ═══ --}}
  <div class="row g-4 mb-4">
    {{-- نمودار ورود/خروج ماهانه --}}
    <div class="col-lg-8">
      <div class="card shadow-none border h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">روند ورود و خروج کالا (۶ ماه اخیر)</h5>
        </div>
        <div class="card-body">
          <div class="chart-container"><canvas id="monthlyChart"></canvas></div>
        </div>
      </div>
    </div>
    {{-- نمودار ABC --}}
    <div class="col-lg-4">
      <div class="card shadow-none border h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">تحلیل ABC انبار</h5>
          <a href="{{ route('warehouse.reports.inventory-valuation') }}" class="btn btn-sm btn-outline-primary">جزئیات</a>
        </div>
        <div class="card-body d-flex flex-column align-items-center">
          <div class="chart-sm w-100"><canvas id="abcChart"></canvas></div>
          <div class="mt-3 w-100">
            <div class="d-flex justify-content-between mb-1">
              <span><span class="badge bg-danger me-1">A</span> {{ $abcChart['counts'][0] }} قلم</span>
              <strong>{{ number_format($abcChart['values'][0]) }} ریال</strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span><span class="badge bg-warning me-1">B</span> {{ $abcChart['counts'][1] }} قلم</span>
              <strong>{{ number_format($abcChart['values'][1]) }} ریال</strong>
            </div>
            <div class="d-flex justify-content-between">
              <span><span class="badge bg-secondary me-1">C</span> {{ $abcChart['counts'][2] }} قلم</span>
              <strong>{{ number_format($abcChart['values'][2]) }} ریال</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══ Charts Row 2 ═══ --}}
  <div class="row g-4 mb-4">
    {{-- روند موجودی ماهانه --}}
    <div class="col-lg-7">
      <div class="card shadow-none border h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">روند موجودی ماهانه (تجمعی)</h5>
          <a href="{{ route('warehouse.reports.stock-card') }}" class="btn btn-sm btn-outline-secondary">کارت موجودی</a>
        </div>
        <div class="card-body">
          <div class="chart-container"><canvas id="stockTrendChart"></canvas></div>
        </div>
      </div>
    </div>
    {{-- محصولات پرفروش --}}
    <div class="col-lg-5">
      <div class="card shadow-none border h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">محصولات پرفروش این ماه</h5>
          <a href="{{ route('warehouse.reports.profit-loss') }}" class="btn btn-sm btn-outline-success">سود/زیان</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light"><tr><th>#</th><th>کالا</th><th class="text-end">تعداد</th><th class="text-end">ارزش</th></tr></thead>
              <tbody>
                @forelse($topSelling as $i => $p)
                <tr>
                  <td>{{ $i+1 }}</td>
                  <td class="fw-medium">{{ $p->product }}</td>
                  <td class="text-end">{{ number_format($p->total_qty,1) }}</td>
                  <td class="text-end text-success fw-medium">{{ number_format($p->total_value) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-3">فروشی ثبت نشده.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══ Bottom Row ═══ --}}
  <div class="row g-4">
    {{-- کالاهای زیر حداقل --}}
    <div class="col-lg-6">
      <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between">
          <h5 class="mb-0 text-danger"><i class="bx bx-error-circle me-1"></i> کالاهای زیر حداقل موجودی</h5>
          <a href="{{ route('warehouse.reports.below-minimum') }}" class="btn btn-sm btn-outline-danger">مشاهده کامل</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light"><tr><th>کالا</th><th>انبار</th><th>موجودی</th><th>حداقل</th><th>کمبود</th></tr></thead>
              <tbody>
                @forelse($belowMin as $item)
                <tr>
                  <td>{{ $item->product }}</td>
                  <td>{{ $item->warehouse }}</td>
                  <td class="text-danger fw-bold">{{ number_format($item->current_stock,1) }}</td>
                  <td>{{ number_format($item->minimum_stock,1) }}</td>
                  <td class="text-danger">{{ number_format($item->minimum_stock - $item->current_stock, 1) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">همه کالاها بالای حداقل هستند ✓</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    {{-- آخرین اسناد --}}
    <div class="col-lg-6">
      <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between">
          <h5 class="mb-0">آخرین اسناد انبار</h5>
          <a href="{{ route('warehouse.documents.index') }}" class="btn btn-sm btn-outline-primary">مشاهده همه</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light"><tr><th>شماره</th><th>انبار</th><th>نوع</th><th>وضعیت</th><th>تاریخ</th></tr></thead>
              <tbody>
                @forelse($recentDocs as $doc)
                <tr>
                  <td><a href="{{ route('warehouse.documents.show', $doc) }}">{{ $doc->document_number }}</a></td>
                  <td>{{ $doc->warehouse?->title }}</td>
                  <td>{{ $doc->type ?? '—' }}</td>
                  <td><span class="badge bg-label-{{ $doc->status === 'approved' ? 'success' : ($doc->status === 'pending' ? 'warning' : 'secondary') }}">{{ $doc->status }}</span></td>
                  <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">سند ثبت نشده.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const rtlFont = { family: 'Tahoma, Arial, sans-serif' };

// ─── نمودار ورود/خروج ─────────────────────────────────────────
new Chart(document.getElementById('monthlyChart'), {
  type: 'bar',
  data: {
    labels: @json($monthlyChart['labels']),
    datasets: [
      { label: 'ورود', data: @json($monthlyChart['inData']),  backgroundColor: 'rgba(105,108,255,.7)', borderRadius: 4 },
      { label: 'خروج', data: @json($monthlyChart['outData']), backgroundColor: 'rgba(255,62,29,.6)',   borderRadius: 4 },
    ]
  },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position:'top' } } }
});

// ─── نمودار ABC ───────────────────────────────────────────────
new Chart(document.getElementById('abcChart'), {
  type: 'doughnut',
  data: {
    labels: @json($abcChart['labels']),
    datasets: [{ data: @json($abcChart['values']), backgroundColor: ['#ff3d1d','#ffab00','#8592a3'], hoverOffset: 6 }]
  },
  options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position:'bottom' } } }
});

// ─── روند موجودی ─────────────────────────────────────────────
new Chart(document.getElementById('stockTrendChart'), {
  type: 'line',
  data: {
    labels: @json($stockTrend['labels']),
    datasets: [{
      label: 'موجودی تجمعی',
      data: @json($stockTrend['stockData']),
      borderColor: '#71dd37', backgroundColor: 'rgba(113,221,55,.12)',
      tension: .35, fill: true, pointRadius: 4
    }]
  },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display:false } } }
});
</script>
@endpush
