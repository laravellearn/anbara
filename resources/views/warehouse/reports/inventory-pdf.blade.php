<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>گزارش موجودی لحظه‌ای</title>
<style>
  @page { margin: 18mm 14mm; }
  * { box-sizing: border-box; }
  body { font-family: Tahoma, Arial, sans-serif; font-size: 11px; color: #222; direction: rtl; margin: 0; }

  /* ─── سربرگ ─── */
  .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 14px; }
  .header .logo-area { display: flex; align-items: center; gap: 10px; }
  .header .logo-area img { height: 50px; object-fit: contain; }
  .header .org-name { font-size: 16px; font-weight: bold; }
  .header .report-meta { text-align: left; font-size: 10px; color: #555; line-height: 1.8; }
  .report-title { text-align: center; font-size: 14px; font-weight: bold; margin: 10px 0 14px; border: 1px solid #ccc; padding: 6px; border-radius: 4px; background: #f5f5f5; }

  /* ─── فیلترهای اعمال‌شده ─── */
  .filters-applied { font-size: 10px; color: #666; margin-bottom: 10px; background: #fafafa; border: 1px dashed #ccc; padding: 5px 10px; border-radius: 3px; }

  /* ─── جدول ─── */
  table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  thead tr { background: #2d3a5e; color: #fff; }
  thead th { padding: 6px 8px; font-size: 10px; font-weight: bold; border: 1px solid #2d3a5e; }
  tbody tr:nth-child(even) { background: #f9f9f9; }
  tbody td { padding: 5px 8px; border: 1px solid #ddd; font-size: 10px; }
  tbody tr.below-min td { color: #c0392b; font-weight: bold; }
  .text-center { text-align: center; }
  .text-left  { text-align: left; }

  /* ─── خلاصه ─── */
  .summary { display: flex; gap: 10px; margin-bottom: 14px; }
  .summary .box { flex: 1; border: 1px solid #ccc; border-radius: 4px; padding: 8px 12px; text-align: center; }
  .summary .box .val { font-size: 18px; font-weight: bold; }
  .summary .box .lbl { font-size: 10px; color: #666; }
  .summary .box.danger .val { color: #c0392b; }

  /* ─── فوتر ─── */
  .footer { border-top: 1px solid #ccc; padding-top: 8px; font-size: 9px; color: #888; display: flex; justify-content: space-between; }

  @media print {
    .no-print { display: none !important; }
    a { text-decoration: none !important; color: inherit !important; }
  }
</style>
</head>
<body>

{{-- دکمه چاپ (نمایش فقط در مرورگر) --}}
<div class="no-print" style="text-align:left; margin-bottom:14px;">
  <button onclick="window.print()" style="padding:8px 20px; background:#2d3a5e; color:#fff; border:none; border-radius:4px; cursor:pointer; font-family:Tahoma; font-size:12px;">
    🖨️ چاپ / ذخیره PDF
  </button>
  <a href="{{ route('warehouse.reports.inventory') }}" style="margin-right:10px; font-size:12px; color:#555;">← بازگشت</a>
</div>

{{-- سربرگ --}}
<div class="header">
  <div class="logo-area">
    @if(isset($tenant) && $tenant->logo_path)
      <img src="{{ asset('storage/'.$tenant->logo_path) }}" alt="لوگو">
    @endif
    <div>
      <div class="org-name">{{ $tenant->title ?? $tenant->name ?? config('app.name') }}</div>
      @if(isset($tenant) && $tenant->address)
        <div style="font-size:10px; color:#666; margin-top:3px;">{{ $tenant->address }}</div>
      @endif
    </div>
  </div>
  <div class="report-meta">
    <div><strong>تاریخ گزارش:</strong> {{ verta(now())->format('Y/m/d') }}</div>
    <div><strong>ساعت:</strong> {{ now()->format('H:i') }}</div>
    <div><strong>تهیه‌کننده:</strong> {{ auth()->user()?->name ?? '—' }}</div>
  </div>
</div>

<div class="report-title">گزارش موجودی لحظه‌ای انبار</div>

{{-- فیلترهای فعال --}}
@php
  $activeFilters = array_filter([
    'انبار' => $selectedWarehouse?->title ?? null,
    'دسته‌بندی' => $selectedCategory?->title ?? null,
    'کالا' => request('product_search') ?: null,
  ]);
@endphp
@if(count($activeFilters))
<div class="filters-applied">
  <strong>فیلترهای اعمال‌شده:</strong>
  @foreach($activeFilters as $k => $v) {{ $k }}: <strong>{{ $v }}</strong> &nbsp;|&nbsp; @endforeach
</div>
@endif

{{-- خلاصه --}}
<div class="summary">
  <div class="box">
    <div class="val">{{ $rows->count() }}</div>
    <div class="lbl">تعداد اقلام</div>
  </div>
  <div class="box danger">
    <div class="val">{{ $rows->filter(fn($r) => $r->current_stock < $r->minimum_stock && $r->minimum_stock > 0)->count() }}</div>
    <div class="lbl">زیر حداقل موجودی</div>
  </div>
  <div class="box">
    <div class="val">{{ $rows->filter(fn($r) => $r->current_stock <= 0)->count() }}</div>
    <div class="lbl">موجودی صفر</div>
  </div>
</div>

{{-- جدول --}}
<table>
  <thead>
    <tr>
      <th class="text-center">#</th>
      <th>کد کالا</th>
      <th>نام کالا</th>
      <th>دسته‌بندی</th>
      <th>انبار</th>
      <th class="text-center">موجودی فعلی</th>
      <th class="text-center">حداقل موجودی</th>
      <th class="text-center">واحد</th>
      <th class="text-center">وضعیت</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rows as $i => $row)
    <tr class="{{ $row->current_stock < $row->minimum_stock && $row->minimum_stock > 0 ? 'below-min' : '' }}">
      <td class="text-center">{{ $i + 1 }}</td>
      <td>{{ $row->sku ?? '—' }}</td>
      <td>{{ $row->product_title }}</td>
      <td>{{ $row->category ?? '—' }}</td>
      <td>{{ $row->warehouse_title }}</td>
      <td class="text-center"><strong>{{ number_format($row->current_stock, 2) }}</strong></td>
      <td class="text-center">{{ $row->minimum_stock > 0 ? number_format($row->minimum_stock, 2) : '—' }}</td>
      <td class="text-center">{{ $row->unit ?? '—' }}</td>
      <td class="text-center">
        @if($row->current_stock <= 0)
          <span style="color:#c0392b; font-weight:bold;">صفر</span>
        @elseif($row->minimum_stock > 0 && $row->current_stock < $row->minimum_stock)
          <span style="color:#e67e22; font-weight:bold;">کمتر از حداقل</span>
        @else
          <span style="color:#27ae60;">مناسب</span>
        @endif
      </td>
    </tr>
    @empty
    <tr><td colspan="9" class="text-center" style="padding:16px; color:#888;">داده‌ای یافت نشد.</td></tr>
    @endforelse
  </tbody>
</table>

{{-- فوتر --}}
<div class="footer">
  <span>سیستم انبارداری — {{ config('app.name') }}</span>
  <span>تاریخ چاپ: {{ verta(now())->format('l، j F Y — H:i') }}</span>
</div>

</body>
</html>
