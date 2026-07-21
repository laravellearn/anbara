<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>فاکتور فروش {{ $salesInvoice->invoice_number }}</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; font-size: 12px; direction: rtl; }
  .header { text-align: center; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
  th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: right; }
  th { background: #f0f0f0; }
  .totals td { border: none; padding: 4px 8px; }
  .totals .total-row { font-weight: bold; font-size: 14px; }
  @media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="no-print" style="margin-bottom:15px">
  <button onclick="window.print()">چاپ</button>
  <button onclick="window.close()">بستن</button>
</div>

<div class="header">
  <h2>فاکتور فروش</h2>
  <p>شماره: {{ $salesInvoice->invoice_number }} | تاریخ: {{ $salesInvoice->invoice_date->format('Y-m-d') }}</p>
</div>

<table>
  <tr><th>مشتری</th><td>{{ $salesInvoice->customer?->name ?? '—' }}</td><th>انبار</th><td>{{ $salesInvoice->warehouse?->title ?? '—' }}</td></tr>
  <tr><th>سررسید</th><td>{{ $salesInvoice->due_date?->format('Y-m-d') ?? '—' }}</td><th>شماره مرجع</th><td>{{ $salesInvoice->reference_number ?? '—' }}</td></tr>
</table>

<table>
  <thead>
    <tr><th>#</th><th>کالا</th><th>واحد</th><th>مقدار</th><th>قیمت واحد</th><th>تخفیف</th><th>جمع</th></tr>
  </thead>
  <tbody>
    @foreach($salesInvoice->items as $i => $item)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $item->product?->title }}</td>
      <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
      <td>{{ number_format($item->quantity, 2) }}</td>
      <td>{{ number_format($item->unit_price) }}</td>
      <td>{{ number_format($item->discount_amount) }}</td>
      <td>{{ number_format($item->total_price) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<table class="totals" style="width:300px;float:left">
  <tr><td>جمع اقلام:</td><td>{{ number_format($salesInvoice->subtotal) }}</td></tr>
  <tr><td>تخفیف ({{ $salesInvoice->discount_percent }}%):</td><td>{{ number_format($salesInvoice->discount_amount) }}</td></tr>
  <tr><td>مالیات ({{ $salesInvoice->tax_percent }}%):</td><td>{{ number_format($salesInvoice->tax_amount) }}</td></tr>
  <tr class="total-row"><td>جمع نهایی:</td><td>{{ number_format($salesInvoice->total_amount) }}</td></tr>
</table>

@if($salesInvoice->description)
<div style="clear:both;margin-top:20px"><strong>توضیحات:</strong> {{ $salesInvoice->description }}</div>
@endif
</body>
</html>
