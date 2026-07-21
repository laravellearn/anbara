@extends('layouts.app')
@section('title', 'فاکتورهای فروش')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- KPI cards --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
      <div class="card text-center h-100">
        <div class="card-body"><h6 class="text-muted">کل فاکتورها</h6><h3>{{ number_format($stats['total']) }}</h3></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card text-center h-100">
        <div class="card-body"><h6 class="text-muted">پیش‌نویس</h6><h3 class="text-secondary">{{ number_format($stats['draft']) }}</h3></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card text-center h-100">
        <div class="card-body"><h6 class="text-muted">پرداخت نشده</h6><h3 class="text-warning">{{ number_format($stats['unpaid']) }}</h3></div>
      </div>
    </div>
    <div class="col-6 col-xl-3">
      <div class="card text-center h-100">
        <div class="card-body"><h6 class="text-muted">درآمد (تسویه)</h6><h3 class="text-success">{{ number_format($stats['total_revenue']) }}</h3></div>
      </div>
    </div>
  </div>

  {{-- فیلتر --}}
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label small">جستجو</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="شماره فاکتور..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small">وضعیت</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">همه</option>
            @foreach(\App\Models\SalesInvoice::statusLabels() as $k => $v)
              <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">مشتری</label>
          <select name="customer_id" class="form-select form-select-sm">
            <option value="">همه</option>
            @foreach($customers as $c)
              <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">از تاریخ</label>
          <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small">تا تاریخ</label>
          <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1 d-flex gap-1">
          <button class="btn btn-primary btn-sm">فیلتر</button>
          <a href="{{ route('warehouse.sales-invoices.index') }}" class="btn btn-outline-secondary btn-sm">پاک</a>
        </div>
      </form>
    </div>
  </div>

  {{-- جدول --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">فاکتورهای فروش</h5>
      <div class="d-flex gap-2">
        <a href="{{ route('warehouse.export.sales-invoices-csv') . '?' . http_build_query(request()->all()) }}" class="btn btn-outline-success btn-sm">
          <i data-feather="download" class="me-1"></i>خروجی CSV
        </a>
        @can('access', 'sales-invoices.create')
        <a href="{{ route('warehouse.sales-invoices.create') }}" class="btn btn-primary btn-sm">
          <i data-feather="plus" class="me-1"></i>فاکتور جدید
        </a>
        @endcan
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>شماره فاکتور</th><th>تاریخ</th><th>مشتری</th>
            <th class="text-end">جمع کل</th><th class="text-end">مانده</th>
            <th>وضعیت</th><th>عملیات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
          @php $colors = \App\Models\SalesInvoice::statusColors(); $labels = \App\Models\SalesInvoice::statusLabels(); @endphp
          <tr>
            <td><a href="{{ route('warehouse.sales-invoices.show', $inv) }}">{{ $inv->invoice_number }}</a></td>
            <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
            <td>{{ $inv->customer?->name ?? '—' }}</td>
            <td class="text-end">{{ number_format($inv->total_amount) }}</td>
            <td class="text-end">{{ number_format($inv->remainingAmount()) }}</td>
            <td><span class="badge bg-label-{{ $colors[$inv->status] ?? 'secondary' }}">{{ $labels[$inv->status] ?? $inv->status }}</span></td>
            <td>
              <a href="{{ route('warehouse.sales-invoices.show', $inv) }}" class="btn btn-xs btn-icon btn-outline-primary" title="مشاهده"><i data-feather="eye"></i></a>
              @if($inv->isEditable())
              <a href="{{ route('warehouse.sales-invoices.edit', $inv) }}" class="btn btn-xs btn-icon btn-outline-warning" title="ویرایش"><i data-feather="edit"></i></a>
              @endif
              <a href="{{ route('warehouse.sales-invoices.print', $inv) }}" target="_blank" class="btn btn-xs btn-icon btn-outline-secondary" title="چاپ"><i data-feather="printer"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-4">فاکتوری یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer">{{ $invoices->links() }}</div>
    @endif
  </div>
</div>
@endsection
