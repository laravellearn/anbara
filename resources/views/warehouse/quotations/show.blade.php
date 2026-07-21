@extends('layouts.app')
@section('title', 'پیش‌فاکتور '.$quotation->quotation_number)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @php $colors=\App\Models\Quotation::statusColors();$labels=\App\Models\Quotation::statusLabels(); @endphp
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <h4 class="fw-bold mb-0">پیش‌فاکتور {{ $quotation->quotation_number }}
      <span class="badge bg-label-{{ $colors[$quotation->status]??'secondary' }} ms-2">{{ $labels[$quotation->status]??$quotation->status }}</span>
    </h4>
    <div class="d-flex gap-2 flex-wrap">
      @if($quotation->isEditable())
        <a href="{{ route('warehouse.quotations.edit',$quotation) }}" class="btn btn-warning btn-sm">ویرایش</a>
        <form action="{{ route('warehouse.quotations.status',$quotation) }}" method="POST" class="d-inline">
          @csrf
          <input type="hidden" name="status" value="sent">
          <button class="btn btn-info btn-sm" onclick="return confirm('ارسال شود؟')">علامت‌گذاری ارسال شده</button>
        </form>
        <form action="{{ route('warehouse.quotations.status',$quotation) }}" method="POST" class="d-inline">
          @csrf
          <input type="hidden" name="status" value="accepted">
          <button class="btn btn-success btn-sm" onclick="return confirm('پذیرفته شود؟')">تأیید مشتری</button>
        </form>
      @endif
      @if($quotation->canConvert())
        <form action="{{ route('warehouse.quotations.convert',$quotation) }}" method="POST" class="d-inline">
          @csrf
          <button class="btn btn-primary btn-sm" onclick="return confirm('تبدیل به فاکتور فروش شود؟')">تبدیل به فاکتور</button>
        </form>
      @endif
      @if($quotation->salesInvoice)
        <a href="{{ route('warehouse.sales-invoices.show',$quotation->salesInvoice) }}" class="btn btn-outline-primary btn-sm">مشاهده فاکتور</a>
      @endif
      <a href="{{ route('warehouse.quotations.print',$quotation) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i data-feather="printer"></i></a>
      <a href="{{ route('warehouse.quotations.index') }}" class="btn btn-outline-secondary btn-sm">بازگشت</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-6">
      <div class="card h-100"><div class="card-header"><h6 class="mb-0">اطلاعات پیش‌فاکتور</h6></div>
        <div class="card-body">
          <table class="table table-sm">
            <tr><td class="text-muted">شماره</td><td>{{ $quotation->quotation_number }}</td></tr>
            <tr><td class="text-muted">تاریخ</td><td>{{ $quotation->quotation_date->format('Y-m-d') }}</td></tr>
            <tr><td class="text-muted">اعتبار تا</td><td>{{ $quotation->valid_until?->format('Y-m-d') ?? '—' }}</td></tr>
            <tr><td class="text-muted">مشتری</td><td>{{ $quotation->customer?->name ?? '—' }}</td></tr>
            <tr><td class="text-muted">انبار</td><td>{{ $quotation->warehouse?->title ?? '—' }}</td></tr>
            <tr><td class="text-muted">ثبت‌کننده</td><td>{{ $quotation->creator?->name ?? '—' }}</td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card h-100"><div class="card-header"><h6 class="mb-0">خلاصه مالی</h6></div>
        <div class="card-body">
          <table class="table table-sm">
            <tr><td class="text-muted">جمع اقلام</td><td class="text-end">{{ number_format($quotation->subtotal) }}</td></tr>
            <tr><td class="text-muted">تخفیف ({{ $quotation->discount_percent }}%)</td><td class="text-end text-danger">{{ number_format($quotation->discount_amount) }}</td></tr>
            <tr><td class="text-muted">مالیات ({{ $quotation->tax_percent }}%)</td><td class="text-end">{{ number_format($quotation->tax_amount) }}</td></tr>
            <tr class="fw-bold"><td>جمع کل</td><td class="text-end">{{ number_format($quotation->total_amount) }}</td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card"><div class="card-header"><h6 class="mb-0">اقلام</h6></div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr><th>#</th><th>کالا</th><th>واحد</th><th class="text-end">مقدار</th><th class="text-end">قیمت واحد</th><th class="text-end">تخفیف</th><th class="text-end">جمع</th></tr>
            </thead>
            <tbody>
              @foreach($quotation->items as $i=>$item)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->product?->title }}</td>
                <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                <td class="text-end">{{ number_format($item->quantity,2) }}</td>
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
@endsection
