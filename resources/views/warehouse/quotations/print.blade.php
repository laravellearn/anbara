<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>پیش‌فاکتور {{ $quotation->quotation_number }}</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; font-size: 12px; direction: rtl; }
  .header { text-align: center; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
  th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: right; }
  th { background: #f0f0f0; }
  .totals td { border: none; padding: 4px 8px; }
  .total-row { font-weight: bold; font-size: 14px; }
  .terms { margin-top: 20px; padding: 10px; background: #f9f9f9; border: 1px solid #eee; }
  @media print { .no-print { display: none; } }
</style>
</head>
<body>
<div class="no-print" style="margin-bottom:15px">
  <button onclick="window.print()">چاپ</button>
  <button onclick="window.close()">بستن</button>
</div>
<div class="header">
@php
    $tenantId   = app(\App\Services\TenantManager::class)->getTenantId();
    $orgName    = \App\Models\TenantSetting::get($tenantId, 'org_name', config('app.name'));
    $orgLogo    = \App\Models\TenantSetting::get($tenantId, 'org_logo', '');
    $brandColor = \App\Models\TenantSetting::get($tenantId, 'org_brand_color', '#3B82F6');
    $orgPhone   = \App\Models\TenantSetting::get($tenantId, 'org_phone', '');
    $orgAddress = \App\Models\TenantSetting::get($tenantId, 'org_address', '');
@endphp
<div style="display:flex;justify-content:space-between;align-items:center;border-bottom:3px solid {{ $brandColor }};padding-bottom:10px;margin-bottom:14px">
  <div>
    <h2 style="color:{{ $brandColor }};margin:0 0 4px;font-size:16px">{{ $orgName }}</h2>
    @if($orgPhone)<p style="font-size:10px;color:#555;margin:2px 0">📞 {{ $orgPhone }}</p>@endif
    @if($orgAddress)<p style="font-size:10px;color:#555;margin:2px 0">📍 {{ $orgAddress }}</p>@endif
  </div>
  <div style="text-align:left">
    @if($orgLogo)
      <img src="{{ asset('storage/'.$orgLogo) }}" style="max-height:60px;max-width:150px;display:block;margin-bottom:4px" alt="{{ $orgName }}">
    @endif
    <div style="font-weight:bold;color:{{ $brandColor }}">پیش‌فاکتور (Quotation)</div>
    <div style="font-size:11px;color:#555">شماره: {{ $quotation->quotation_number }} | تاریخ: {{ $quotation->quotation_date->format('Y-m-d') }}</div>
  </div>
</div>
<table>
  <tr>
    <th>مشتری</th><td>{{ $quotation->customer?->name ?? '—' }}</td>
    <th>اعتبار تا</th><td>{{ $quotation->valid_until?->format('Y-m-d') ?? '—' }}</td>
  </tr>
  <tr>
    <th>انبار</th><td>{{ $quotation->warehouse?->title ?? '—' }}</td>
    <th>شماره مرجع</th><td>{{ $quotation->reference_number ?? '—' }}</td>
  </tr>
</table>
<table>
  <thead>
    <tr><th>#</th><th>کالا</th><th>واحد</th><th>مقدار</th><th>قیمت واحد</th><th>تخفیف</th><th>جمع</th></tr>
  </thead>
  <tbody>
    @foreach($quotation->items as $i => $item)
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
<table class="totals" style="width:280px;float:left">
  <tr><td>جمع اقلام:</td><td>{{ number_format($quotation->subtotal) }}</td></tr>
  <tr><td>تخفیف ({{ $quotation->discount_percent }}%):</td><td>{{ number_format($quotation->discount_amount) }}</td></tr>
  <tr><td>مالیات ({{ $quotation->tax_percent }}%):</td><td>{{ number_format($quotation->tax_amount) }}</td></tr>
  <tr class="total-row"><td>جمع نهایی:</td><td>{{ number_format($quotation->total_amount) }}</td></tr>
</table>
@if($quotation->terms)
<div class="terms" style="clear:both;margin-top:20px">
  <strong>شرایط و ضوابط:</strong> {{ $quotation->terms }}
</div>
@endif
</body>
</html>
