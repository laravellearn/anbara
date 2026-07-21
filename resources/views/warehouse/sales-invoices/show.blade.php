@extends('layouts.app')
@section('title', 'فاکتور فروش — ' . $salesInvoice->invoice_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @php $colors = \App\Models\SalesInvoice::statusColors(); $labels = \App\Models\SalesInvoice::statusLabels(); @endphp

  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h4 class="fw-bold mb-0">فاکتور {{ $salesInvoice->invoice_number }}
      <span class="badge bg-label-{{ $colors[$salesInvoice->status] ?? 'secondary' }} ms-2">{{ $labels[$salesInvoice->status] ?? $salesInvoice->status }}</span>
    </h4>
    <div class="d-flex gap-2 flex-wrap">
      @if($salesInvoice->canConfirm())
      @can('access','sales-invoices.confirm')
      <form action="{{ route('warehouse.sales-invoices.confirm', $salesInvoice) }}" method="POST" class="d-inline">
        @csrf
        <input type="hidden" name="issue_document" value="1">
        <button class="btn btn-success btn-sm" onclick="return confirm('تأیید شود؟')">تأیید و صدور حواله</button>
      </form>
      <form action="{{ route('warehouse.sales-invoices.confirm', $salesInvoice) }}" method="POST" class="d-inline">
        @csrf
        <button class="btn btn-outline-success btn-sm" onclick="return confirm('تأیید شود؟')">تأیید (بدون حواله)</button>
      </form>
      @endcan
      @endif

      @if(in_array($salesInvoice->status, ['confirmed','partially_paid']))
      @can('access','sales-invoices.pay')
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">ثبت پرداخت</button>
      @endcan
      @endif

      <a href="{{ route('warehouse.sales-invoices.print', $salesInvoice) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
        <i data-feather="printer"></i> چاپ
      </a>

      @if($salesInvoice->isEditable())
      <a href="{{ route('warehouse.sales-invoices.edit', $salesInvoice) }}" class="btn btn-outline-warning btn-sm">
        <i data-feather="edit"></i> ویرایش
      </a>
      @endif

      @if($salesInvoice->canCancel())
      <form action="{{ route('warehouse.sales-invoices.cancel', $salesInvoice) }}" method="POST">
        @csrf
        <button class="btn btn-outline-danger btn-sm" onclick="return confirm('لغو شود؟')">لغو فاکتور</button>
      </form>
      @endif

      <a href="{{ route('warehouse.sales-invoices.index') }}" class="btn btn-outline-secondary btn-sm">بازگشت</a>
    </div>
  </div>

  <div class="row g-3">
    {{-- اطلاعات اصلی --}}
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header"><h6 class="mb-0">اطلاعات فاکتور</h6></div>
        <div class="card-body">
          <table class="table table-sm">
            <tr><td class="text-muted">شماره فاکتور</td><td>{{ $salesInvoice->invoice_number }}</td></tr>
            <tr><td class="text-muted">تاریخ</td><td>{{ $salesInvoice->invoice_date->format('Y-m-d') }}</td></tr>
            <tr><td class="text-muted">سررسید</td><td>{{ $salesInvoice->due_date?->format('Y-m-d') ?? '—' }}</td></tr>
            <tr><td class="text-muted">مشتری</td><td>{{ $salesInvoice->customer?->name ?? '—' }}</td></tr>
            <tr><td class="text-muted">انبار</td><td>{{ $salesInvoice->warehouse?->title ?? '—' }}</td></tr>
            <tr><td class="text-muted">شماره مرجع</td><td>{{ $salesInvoice->reference_number ?? '—' }}</td></tr>
            <tr><td class="text-muted">توضیحات</td><td>{{ $salesInvoice->description ?? '—' }}</td></tr>
            <tr><td class="text-muted">ثبت‌کننده</td><td>{{ $salesInvoice->creator?->name ?? '—' }}</td></tr>
            @if($salesInvoice->confirmer)
            <tr><td class="text-muted">تأییدکننده</td><td>{{ $salesInvoice->confirmer->name }} ({{ $salesInvoice->confirmed_at->format('Y-m-d') }})</td></tr>
            @endif
          </table>
        </div>
      </div>
    </div>

    {{-- خلاصه مالی --}}
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-header"><h6 class="mb-0">خلاصه مالی</h6></div>
        <div class="card-body">
          <table class="table table-sm">
            <tr><td class="text-muted">جمع اقلام</td><td class="text-end">{{ number_format($salesInvoice->subtotal) }}</td></tr>
            <tr><td class="text-muted">تخفیف ({{ $salesInvoice->discount_percent }}%)</td><td class="text-end text-danger">{{ number_format($salesInvoice->discount_amount) }}</td></tr>
            <tr><td class="text-muted">مالیات ({{ $salesInvoice->tax_percent }}%)</td><td class="text-end">{{ number_format($salesInvoice->tax_amount) }}</td></tr>
            <tr class="fw-bold"><td>جمع کل</td><td class="text-end">{{ number_format($salesInvoice->total_amount) }}</td></tr>
            <tr class="text-success"><td>پرداخت شده</td><td class="text-end">{{ number_format($salesInvoice->paid_amount) }}</td></tr>
            <tr class="text-danger fw-bold"><td>مانده</td><td class="text-end">{{ number_format($salesInvoice->remainingAmount()) }}</td></tr>
          </table>
        </div>
      </div>
    </div>

    {{-- اقلام --}}
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h6 class="mb-0">اقلام فاکتور</h6></div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr><th>#</th><th>کالا</th><th>واحد</th><th class="text-end">مقدار</th><th class="text-end">قیمت واحد</th><th class="text-end">تخفیف</th><th class="text-end">جمع</th></tr>
            </thead>
            <tbody>
              @foreach($salesInvoice->items as $i => $item)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product?->title }}</td>
                <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-end">{{ number_format($item->unit_price) }}</td>
                <td class="text-end">{{ number_format($item->discount_amount) }}</td>
                <td class="text-end fw-semibold">{{ number_format($item->total_price) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- مودال ثبت پرداخت --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form action="{{ route('warehouse.sales-invoices.pay', $salesInvoice) }}" method="POST">
        @csrf
        <div class="modal-header"><h5 class="modal-title">ثبت پرداخت</h5></div>
        <div class="modal-body">
          <p class="text-muted small">مانده: <strong>{{ number_format($salesInvoice->remainingAmount()) }}</strong></p>
          <label class="form-label">مبلغ دریافتی</label>
          <input type="number" name="amount" class="form-control" step="1" min="1" max="{{ $salesInvoice->remainingAmount() }}" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">انصراف</button>
          <button type="submit" class="btn btn-primary btn-sm">ثبت</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
