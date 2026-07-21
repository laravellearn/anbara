@extends('layouts.master')
@section('title', 'گزارش ارزش موجودی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- KPI --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-4">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted">جمع ارزش موجودی</span>
            <h3 class="mb-0 mt-1 text-primary">{{ number_format($totalValue) }} ﷼</h3>
          </div>
          <span class="badge bg-label-primary rounded p-2"><i class="bx bx-dollar-circle bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-4">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div>
            <span class="fw-medium text-muted">تعداد اقلام موجود</span>
            <h3 class="mb-0 mt-1">{{ $rows->count() }}</h3>
          </div>
          <span class="badge bg-label-info rounded p-2"><i class="bx bx-package bx-sm"></i></span>
        </div>
      </div>
    </div>
  </div>

  {{-- فیلترها --}}
  <div class="card shadow-none border mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('warehouse.reports.stock-value') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-medium">انبار</label>
            <select name="warehouse_id" class="form-select">
              <option value="">همه انبارها</option>
              @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-medium">دسته‌بندی</label>
            <select name="category_id" class="form-select">
              <option value="">همه</option>
              @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->title }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> اعمال</button>
            <a href="{{ route('warehouse.reports.stock-value') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
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

  <div class="card shadow-none border">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0"><i class="bx bx-dollar-circle me-1"></i> ارزش موجودی کالاها</h5>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>کالا</th>
            <th>کد</th>
            <th>دسته</th>
            <th>انبار</th>
            <th>واحد</th>
            <th class="text-end">موجودی</th>
            <th class="text-end">قیمت واحد (ریال)</th>
            <th class="text-end text-primary">ارزش کل (ریال)</th>
            <th>درصد از کل</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $i => $row)
          @php $pct = $totalValue > 0 ? round(($row->stock_value / $totalValue) * 100, 1) : 0; @endphp
          <tr>
            <td><small class="text-muted">{{ $i + 1 }}</small></td>
            <td class="fw-medium">{{ $row->product_title }}</td>
            <td><small class="text-muted">{{ $row->sku ?? '—' }}</small></td>
            <td><small>{{ $row->category ?? '—' }}</small></td>
            <td>{{ $row->warehouse_title }}</td>
            <td><small>{{ $row->unit ?? '—' }}</small></td>
            <td class="text-end">{{ number_format($row->current_stock, 2) }}</td>
            <td class="text-end">{{ number_format($row->last_unit_price) }}</td>
            <td class="text-end fw-bold text-primary">{{ number_format($row->stock_value) }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1" style="height:6px">
                  <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                </div>
                <small>{{ $pct }}%</small>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="10" class="text-center text-muted py-5">داده‌ای یافت نشد.</td></tr>
          @endforelse
        </tbody>
        @if($rows->count())
        <tfoot class="table-light fw-bold">
          <tr>
            <td colspan="8" class="text-end">جمع کل ارزش موجودی:</td>
            <td class="text-end text-primary">{{ number_format($totalValue) }} ﷼</td>
            <td></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>
  </div>
</div>
@endsection
