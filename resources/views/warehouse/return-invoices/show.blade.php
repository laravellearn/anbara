@extends('layouts.warehouse')

@section('title', 'سند برگشت ' . $returnInvoice->return_number)

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.return-invoices.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="fas fa-arrow-right"></i>
    </a>
    <div>
      <h4 class="mb-0 fw-bold">سند برگشت {{ $returnInvoice->return_number }}</h4>
      <small class="text-muted">{{ $returnInvoice->type_label }}</small>
    </div>
    <div class="ms-auto d-flex gap-2">
      @if($returnInvoice->isDraft())
        @can('access', 'return-invoices.confirm')
        <form action="{{ route('warehouse.return-invoices.confirm', $returnInvoice) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-success"
            onclick="return confirm('تأیید سند برگشت و اعمال موجودی؟')">
            <i class="fas fa-check me-1"></i> تأیید سند
          </button>
        </form>
        @endcan
        @can('access', 'return-invoices.cancel')
        <form action="{{ route('warehouse.return-invoices.cancel', $returnInvoice) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline-danger"
            onclick="return confirm('لغو این سند برگشت؟')">
            <i class="fas fa-times me-1"></i> لغو
          </button>
        </form>
        @endcan
      @endif
    </div>
  </div>

  <div class="row g-4">
    {{-- اطلاعات اصلی --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold d-flex justify-content-between">
          <span>اطلاعات سند</span>
          @php $badge = match($returnInvoice->status){ 'draft'=>'secondary','confirmed'=>'success','cancelled'=>'danger',default=>'secondary'}; @endphp
          <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }}">{{ $returnInvoice->status_label }}</span>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4"><span class="text-muted d-block small">شماره</span><strong>{{ $returnInvoice->return_number }}</strong></div>
            <div class="col-md-4"><span class="text-muted d-block small">نوع</span><strong>{{ $returnInvoice->type_label }}</strong></div>
            <div class="col-md-4"><span class="text-muted d-block small">تاریخ</span><strong>{{ $returnInvoice->return_date->format('Y-m-d') }}</strong></div>
            <div class="col-md-4"><span class="text-muted d-block small">طرف حساب</span><strong>{{ $returnInvoice->contact?->name ?? '—' }}</strong></div>
            <div class="col-md-4"><span class="text-muted d-block small">انبار</span><strong>{{ $returnInvoice->warehouse?->title ?? '—' }}</strong></div>
            <div class="col-md-4"><span class="text-muted d-block small">سال مالی</span><strong>{{ $returnInvoice->fiscalYear?->name ?? '—' }}</strong></div>
            @if($returnInvoice->reason)
            <div class="col-12"><span class="text-muted d-block small">دلیل برگشت</span><strong>{{ $returnInvoice->reason }}</strong></div>
            @endif
            @if($returnInvoice->notes)
            <div class="col-12"><span class="text-muted d-block small">توضیحات</span><p class="mb-0">{{ $returnInvoice->notes }}</p></div>
            @endif
          </div>
        </div>
      </div>

      {{-- اقلام --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">اقلام برگشتی</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th><th>کالا</th><th>واحد</th><th>تعداد</th><th>قیمت واحد</th><th>تخفیف%</th><th class="text-end">جمع ردیف</th>
                </tr>
              </thead>
              <tbody>
                @foreach($returnInvoice->items as $i => $item)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $item->product?->title }}</td>
                  <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                  <td>{{ number_format($item->quantity, 2) }}</td>
                  <td>{{ number_format($item->unit_price) }}</td>
                  <td>{{ $item->discount_percent }}%</td>
                  <td class="text-end fw-medium">{{ number_format($item->line_total) }}</td>
                </tr>
                @endforeach
              </tbody>
              <tfoot class="table-light">
                <tr><td colspan="6" class="text-end fw-semibold">جمع کل:</td><td class="text-end fw-bold">{{ number_format($returnInvoice->subtotal) }}</td></tr>
                @if($returnInvoice->discount_amount > 0)
                <tr><td colspan="6" class="text-end text-danger">تخفیف:</td><td class="text-end text-danger">{{ number_format($returnInvoice->discount_amount) }}</td></tr>
                @endif
                @if($returnInvoice->tax_amount > 0)
                <tr><td colspan="6" class="text-end">مالیات:</td><td class="text-end">{{ number_format($returnInvoice->tax_amount) }}</td></tr>
                @endif
                <tr class="table-primary"><td colspan="6" class="text-end fw-bold">مبلغ قابل برگشت:</td><td class="text-end fw-bold fs-5">{{ number_format($returnInvoice->total_amount) }}</td></tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- سمت راست --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">اطلاعات ثبت</div>
        <div class="card-body">
          <div class="mb-2"><span class="text-muted small d-block">ثبت‌کننده</span>{{ $returnInvoice->creator?->name ?? '—' }}</div>
          <div class="mb-2"><span class="text-muted small d-block">تاریخ ثبت</span>{{ $returnInvoice->created_at->format('Y-m-d H:i') }}</div>
          @if($returnInvoice->confirmer)
          <div class="mb-2"><span class="text-muted small d-block">تأییدکننده</span>{{ $returnInvoice->confirmer->name }}</div>
          <div class="mb-2"><span class="text-muted small d-block">تاریخ تأیید</span>{{ $returnInvoice->confirmed_at?->format('Y-m-d H:i') }}</div>
          @endif
          @if($returnInvoice->salesInvoice)
          <hr>
          <div class="mb-2"><span class="text-muted small d-block">فاکتور فروش مرجع</span>
            <a href="{{ route('warehouse.sales-invoices.show', $returnInvoice->salesInvoice) }}">{{ $returnInvoice->salesInvoice->invoice_number }}</a>
          </div>
          @endif
          @if($returnInvoice->purchaseInvoice)
          <hr>
          <div class="mb-2"><span class="text-muted small d-block">فاکتور خرید مرجع</span>
            <a href="{{ route('warehouse.purchase-invoices.show', $returnInvoice->purchaseInvoice) }}">{{ $returnInvoice->purchaseInvoice->invoice_number }}</a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
