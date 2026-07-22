@extends('layouts.warehouse')
@section('title', 'ارزیابی موجودی')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center mb-4 gap-3">
    <h4 class="mb-0 fw-bold"><i class="fas fa-balance-scale me-2 text-info"></i>ارزیابی موجودی انبار</h4>
  </div>

  {{-- فیلتر --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label small fw-semibold">انبار</label>
          <select name="warehouse_id" class="form-select">
            <option value="">همه انبارها</option>
            @foreach($warehouses as $wh)
            <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold">دسته‌بندی</label>
          <select name="category_id" class="form-select">
            <option value="">همه دسته‌ها</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-semibold">روش ارزیابی</label>
          <select name="method" class="form-select">
            <option value="avg"  {{ $method === 'avg'  ? 'selected' : '' }}>میانگین موزون (Avg)</option>
            <option value="fifo" {{ $method === 'fifo' ? 'selected' : '' }}>اول ورود اول خروج (FIFO)</option>
            <option value="lifo" {{ $method === 'lifo' ? 'selected' : '' }}>آخر ورود اول خروج (LIFO)</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">محاسبه</button>
          <a href="{{ request()->fullUrlWithQuery(['export'=>'excel']) }}" class="btn btn-outline-success"><i class="fas fa-file-excel"></i></a>
        </div>
      </form>
    </div>
  </div>

  {{-- KPI --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card text-center border-0 shadow-sm border-info">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold text-info">{{ number_format($totalValue) }}</div>
          <div class="small text-muted">ارزش کل موجودی (ریال) — روش {{ strtoupper($method) }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold">{{ number_format($rows->count()) }}</div>
          <div class="small text-muted">تعداد اقلام با موجودی مثبت</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center border-0 shadow-sm">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold">{{ $rows->count() > 0 ? number_format($totalValue / $rows->count()) : '—' }}</div>
          <div class="small text-muted">میانگین ارزش هر قلم (ریال)</div>
        </div>
      </div>
    </div>
  </div>

  {{-- جدول --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
      <span class="fw-semibold">جزئیات ارزش موجودی</span>
      <span class="badge bg-info-subtle text-info">روش: {{ strtoupper($method) }}</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th><th>کالا</th><th>کد</th><th>دسته‌بندی</th><th>واحد</th>
              <th class="text-end">موجودی فعلی</th><th class="text-end">قیمت واحد ({{ strtoupper($method) }})</th>
              <th class="text-end">ارزش کل</th><th class="text-end">سهم %</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $i => $r)
            @php $share = $totalValue > 0 ? round(($r->total_value / $totalValue) * 100, 1) : 0; @endphp
            <tr>
              <td>{{ $i+1 }}</td>
              <td class="fw-medium">{{ $r->product_title }}</td>
              <td class="small text-muted">{{ $r->sku ?? '—' }}</td>
              <td>{{ $r->category ?? '—' }}</td>
              <td>{{ $r->unit ?? '—' }}</td>
              <td class="text-end">{{ number_format($r->current_stock, 2) }}</td>
              <td class="text-end">{{ number_format($r->unit_cost, 2) }}</td>
              <td class="text-end fw-bold">{{ number_format($r->total_value) }}</td>
              <td class="text-end">
                <div class="d-flex align-items-center justify-content-end gap-1">
                  <div class="progress flex-fill" style="height:6px;width:60px">
                    <div class="progress-bar bg-info" style="width:{{ min(100,$share*3) }}%"></div>
                  </div>
                  <span class="small">{{ $share }}%</span>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-5">موجودی‌ای یافت نشد.</td></tr>
            @endforelse
          </tbody>
          @if($rows->count())
          <tfoot class="table-light fw-bold">
            <tr>
              <td colspan="7" class="text-end">جمع کل ارزش موجودی:</td>
              <td class="text-end text-info fs-5">{{ number_format($totalValue) }}</td>
              <td class="text-end">100%</td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
