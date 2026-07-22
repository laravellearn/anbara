@extends('layouts.warehouse')
@section('title', 'گزارش سود و زیان فروش')

@push('styles')
<style>.chart-container { position: relative; height: 280px; }</style>
@endpush

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center mb-4 gap-3">
    <h4 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-success"></i>گزارش سود و زیان فروش</h4>
  </div>

  {{-- فیلتر --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2">
          <label class="form-label small fw-semibold">از تاریخ</label>
          <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">تا تاریخ</label>
          <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">انبار</label>
          <select name="warehouse_id" class="form-select">
            <option value="">همه انبارها</option>
            @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">دسته‌بندی</label>
          <select name="category_id" class="form-select">
            <option value="">همه دسته‌ها</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-semibold">روش محاسبه بهای تمام‌شده</label>
          <select name="method" class="form-select">
            <option value="avg"  {{ $method === 'avg'  ? 'selected' : '' }}>میانگین موزون (Avg)</option>
            <option value="fifo" {{ $method === 'fifo' ? 'selected' : '' }}>اول ورود اول خروج (FIFO)</option>
            <option value="lifo" {{ $method === 'lifo' ? 'selected' : '' }}>آخر ورود اول خروج (LIFO)</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">محاسبه</button>
          <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}" class="btn btn-outline-success">
            <i class="fas fa-file-excel"></i>
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- KPI --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-3">
        <div class="fs-4 fw-bold text-primary">{{ number_format($totals['revenue']) }}</div>
        <div class="small text-muted">درآمد فروش (ریال)</div>
      </div></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-3">
        <div class="fs-4 fw-bold text-danger">{{ number_format($totals['cost']) }}</div>
        <div class="small text-muted">بهای تمام‌شده ({{ strtoupper($method) }})</div>
      </div></div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm border-{{ $totals['gross_profit'] >= 0 ? 'success' : 'danger' }}">
        <div class="card-body py-3">
          <div class="fs-4 fw-bold text-{{ $totals['gross_profit'] >= 0 ? 'success' : 'danger' }}">{{ number_format($totals['gross_profit']) }}</div>
          <div class="small text-muted">سود ناخالص (ریال)</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center border-0 shadow-sm"><div class="card-body py-3">
        <div class="fs-4 fw-bold text-{{ $totals['margin'] >= 20 ? 'success' : ($totals['margin'] >= 10 ? 'warning' : 'danger') }}">{{ $totals['margin'] }}%</div>
        <div class="small text-muted">حاشیه سود ناخالص</div>
      </div></div>
    </div>
  </div>

  {{-- نمودار ۱۰ کالای اول --}}
  @if($rows->count())
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent fw-semibold">۱۰ کالای برتر از نظر سود ناخالص</div>
    <div class="card-body"><div class="chart-container"><canvas id="profitChart"></canvas></div></div>
  </div>
  @endif

  {{-- جدول --}}
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th><th>کالا</th><th>دسته‌بندی</th><th class="text-end">تعداد فروش</th>
              <th class="text-end">درآمد</th><th class="text-end">بهای تمام‌شده</th>
              <th class="text-end">سود ناخالص</th><th class="text-end">حاشیه سود</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $i => $r)
            <tr>
              <td>{{ $i+1 }}</td>
              <td class="fw-medium">{{ $r->product_title }}</td>
              <td><span class="badge bg-secondary-subtle text-secondary">{{ $r->category ?? '—' }}</span></td>
              <td class="text-end">{{ number_format($r->sold_qty, 2) }}</td>
              <td class="text-end">{{ number_format($r->revenue) }}</td>
              <td class="text-end text-danger">{{ number_format($r->cost) }}</td>
              <td class="text-end fw-bold text-{{ $r->gross_profit >= 0 ? 'success' : 'danger' }}">{{ number_format($r->gross_profit) }}</td>
              <td class="text-end">
                <span class="badge bg-{{ $r->profit_margin >= 20 ? 'success' : ($r->profit_margin >= 0 ? 'warning' : 'danger') }}-subtle text-{{ $r->profit_margin >= 20 ? 'success' : ($r->profit_margin >= 0 ? 'warning' : 'danger') }}">
                  {{ $r->profit_margin }}%
                </span>
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-5">داده‌ای یافت نشد.</td></tr>
            @endforelse
          </tbody>
          @if($rows->count())
          <tfoot class="table-light fw-bold">
            <tr>
              <td colspan="4" class="text-end">جمع کل:</td>
              <td class="text-end">{{ number_format($totals['revenue']) }}</td>
              <td class="text-end text-danger">{{ number_format($totals['cost']) }}</td>
              <td class="text-end text-{{ $totals['gross_profit'] >= 0 ? 'success' : 'danger' }}">{{ number_format($totals['gross_profit']) }}</td>
              <td class="text-end">{{ $totals['margin'] }}%</td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
@if($rows->count())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const top10 = @json($rows->take(10)->values());
new Chart(document.getElementById('profitChart'), {
  type: 'bar',
  data: {
    labels: top10.map(r => r.product_title),
    datasets: [
      { label: 'درآمد', data: top10.map(r => r.revenue), backgroundColor: 'rgba(105,108,255,.7)', borderRadius: 4 },
      { label: 'بهای تمام‌شده', data: top10.map(r => r.cost), backgroundColor: 'rgba(255,62,29,.6)', borderRadius: 4 },
      { label: 'سود ناخالص', data: top10.map(r => r.gross_profit), backgroundColor: 'rgba(113,221,55,.7)', borderRadius: 4 },
    ]
  },
  options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } } }
});
</script>
@endif
@endpush
