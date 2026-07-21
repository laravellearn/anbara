<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>چاپ سفارش خرید — {{ $po->po_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;600;700&display=swap');
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Vazirmatn', Tahoma, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; direction: rtl; }
  .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm 15mm 20mm; }
  /* ─── هدر ─── */
  .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #1a56db; padding-bottom: 10px; margin-bottom: 16px; }
  .company-info h1 { font-size: 18px; font-weight: 700; color: #1a56db; margin-bottom: 4px; }
  .company-info p { font-size: 10px; color: #555; line-height: 1.5; }
  .doc-info { text-align: left; }
  .doc-info .po-number { font-size: 22px; font-weight: 700; color: #1a56db; }
  .doc-info .po-label  { font-size: 10px; color: #888; }
  .badge-status { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 4px; }
  .badge-draft    { background: #f3f4f6; color: #555; }
  .badge-confirmed{ background: #dbeafe; color: #1e40af; }
  .badge-received { background: #d1fae5; color: #065f46; }
  .badge-cancelled{ background: #fee2e2; color: #991b1b; }
  /* ─── اطلاعات اصلی ─── */
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
  .info-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; }
  .info-box h4 { font-size: 10px; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; border-bottom: 1px solid #f0f0f0; padding-bottom: 4px; }
  .info-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
  .info-row .lbl { color: #666; }
  .info-row .val { font-weight: 600; }
  /* ─── جدول کالا ─── */
  table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  thead th { background: #1a56db; color: #fff; padding: 7px 8px; text-align: right; font-size: 11px; }
  tbody tr:nth-child(even) { background: #f8faff; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
  tfoot td { padding: 6px 8px; font-weight: 600; background: #f9fafb; }
  .text-end { text-align: left; }
  /* ─── خلاصه مالی ─── */
  .financial-summary { margin-right: auto; width: 240px; border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden; }
  .financial-summary .fs-row { display: flex; justify-content: space-between; padding: 6px 12px; border-bottom: 1px solid #f0f0f0; }
  .financial-summary .fs-total { background: #1a56db; color: #fff; font-weight: 700; font-size: 13px; padding: 8px 12px; display: flex; justify-content: space-between; }
  /* ─── یادداشت / شرایط ─── */
  .notes-section { margin-top: 14px; }
  .notes-section h4 { font-size: 10px; color: #888; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
  .notes-section p { border: 1px solid #e5e7eb; border-radius: 4px; padding: 8px; font-size: 11px; color: #555; min-height: 36px; }
  /* ─── امضاها ─── */
  .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px; }
  .sig-box { border-top: 2px solid #ccc; padding-top: 8px; text-align: center; }
  .sig-box .sig-label { font-size: 10px; color: #888; }
  /* ─── footer ─── */
  .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 6px; display: flex; justify-content: space-between; font-size: 9px; color: #aaa; }
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .no-print { display: none !important; }
    .page { padding: 10mm; }
  }
</style>
</head>
<body>

<div class="no-print" style="text-align:center;padding:12px;background:#f0f4ff;border-bottom:1px solid #c7d5f5">
    <button onclick="window.print()" style="background:#1a56db;color:#fff;border:none;padding:8px 24px;border-radius:6px;cursor:pointer;font-size:13px;font-family:Tahoma">
        🖨️ چاپ / ذخیره PDF
    </button>
    <button onclick="history.back()" style="background:#6b7280;color:#fff;border:none;padding:8px 18px;border-radius:6px;cursor:pointer;font-size:13px;margin-right:8px;font-family:Tahoma">
        بازگشت
    </button>
</div>

<div class="page">
    {{-- هدر --}}
    <div class="header">
        <div class="company-info">
            @php
                $tenantId   = app(\App\Services\TenantManager::class)->getTenantId();
                $orgName    = \App\Models\TenantSetting::get($tenantId, 'org_name', config('app.name'));
                $orgLogo    = \App\Models\TenantSetting::get($tenantId, 'org_logo', '');
                $brandColor = \App\Models\TenantSetting::get($tenantId, 'org_brand_color', '#1a56db');
                $orgPhone   = \App\Models\TenantSetting::get($tenantId, 'org_phone', '');
            @endphp
            @if($orgLogo)
                <img src="{{ asset('storage/'.$orgLogo) }}" style="max-height:55px;max-width:140px;margin-bottom:6px;display:block" alt="{{ $orgName }}">
            @endif
            <h1 style="color:{{ $brandColor }}">{{ $orgName }}</h1>
            @if($orgPhone)<p>📞 {{ $orgPhone }}</p>@endif
            <p>سفارش رسمی خرید</p>
        </div>
        <div class="doc-info">
            <div class="po-label">شماره سفارش</div>
            <div class="po-number">{{ $po->po_number }}</div>
            @php
                $colorMap = ['draft'=>'badge-draft','confirmed'=>'badge-confirmed','received'=>'badge-received','cancelled'=>'badge-cancelled'];
                $cls = $colorMap[$po->status] ?? 'badge-draft';
            @endphp
            <span class="badge-status {{ $cls }}">{{ $po->status_label }}</span>
        </div>
    </div>

    {{-- اطلاعات اصلی --}}
    <div class="info-grid">
        <div class="info-box">
            <h4>اطلاعات سفارش</h4>
            <div class="info-row"><span class="lbl">تاریخ سفارش:</span><span class="val">{{ $po->order_date?->format('Y/m/d') }}</span></div>
            <div class="info-row"><span class="lbl">تحویل پیش‌بینی:</span><span class="val">{{ $po->expected_delivery_date?->format('Y/m/d') ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">شماره مرجع:</span><span class="val">{{ $po->reference_number ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">انبار:</span><span class="val">{{ $po->warehouse?->title }}</span></div>
            <div class="info-row"><span class="lbl">مرکز هزینه:</span><span class="val">{{ $po->costCenter?->title ?? '—' }}</span></div>
        </div>
        <div class="info-box">
            <h4>تأمین‌کننده</h4>
            <div class="info-row"><span class="lbl">نام:</span><span class="val">{{ $po->supplier?->name ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">تلفن:</span><span class="val">{{ $po->supplier?->phone ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">ایمیل:</span><span class="val">{{ $po->supplier?->email ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">آدرس:</span><span class="val">{{ $po->supplier?->address ?? '—' }}</span></div>
        </div>
    </div>

    {{-- جدول کالاها --}}
    <table>
        <thead>
            <tr>
                <th style="width:32px">#</th>
                <th>شرح کالا</th>
                <th>کد / SKU</th>
                <th>واحد</th>
                <th class="text-end">مقدار</th>
                <th class="text-end">قیمت واحد</th>
                <th class="text-end">تخفیف%</th>
                <th class="text-end">جمع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->product?->title }}<br>@if($item->description)<small style="color:#888">{{ $item->description }}</small>@endif</td>
                <td><small>{{ $item->product?->sku ?? '—' }}</small></td>
                <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                <td class="text-end">{{ number_format($item->quantity_ordered, 2) }}</td>
                <td class="text-end">{{ $item->unit_price ? number_format($item->unit_price) : '—' }}</td>
                <td class="text-end">{{ $item->discount_percent > 0 ? $item->discount_percent.'%' : '—' }}</td>
                <td class="text-end">{{ $item->unit_price ? number_format($item->line_total) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- خلاصه مالی --}}
    <div style="display:flex;justify-content:flex-end;margin-bottom:16px">
        <div class="financial-summary">
            <div class="fs-row"><span>جمع کالا</span><span>{{ number_format($po->subtotal) }} ﷼</span></div>
            @if($po->discount_percent > 0)
            <div class="fs-row"><span>تخفیف ({{ $po->discount_percent }}%)</span><span style="color:#dc2626">- {{ number_format($po->discount_amount) }} ﷼</span></div>
            @endif
            @if($po->tax_percent > 0)
            <div class="fs-row"><span>مالیات ({{ $po->tax_percent }}%)</span><span>{{ number_format($po->tax_amount) }} ﷼</span></div>
            @endif
            @if($po->shipping_cost > 0)
            <div class="fs-row"><span>هزینه حمل</span><span>{{ number_format($po->shipping_cost) }} ﷼</span></div>
            @endif
            <div class="fs-total"><span>جمع نهایی</span><span>{{ number_format($po->total_amount) }} ﷼</span></div>
        </div>
    </div>

    {{-- یادداشت --}}
    @if($po->notes || $po->terms_and_conditions)
    <div class="notes-section">
        @if($po->notes)
        <h4>یادداشت</h4>
        <p style="margin-bottom:8px">{{ $po->notes }}</p>
        @endif
        @if($po->terms_and_conditions)
        <h4>شرایط و مقررات</h4>
        <p>{{ $po->terms_and_conditions }}</p>
        @endif
    </div>
    @endif

    {{-- امضاها --}}
    <div class="signatures">
        <div class="sig-box"><div class="sig-label">تنظیم‌کننده<br><small>{{ $po->creator?->name }}</small></div></div>
        <div class="sig-box"><div class="sig-label">تأییدکننده<br><small>{{ $po->confirmer?->name ?? '—' }}</small></div></div>
        <div class="sig-box"><div class="sig-label">تأمین‌کننده</div></div>
    </div>

    {{-- footer --}}
    <div class="footer">
        <span>تاریخ چاپ: {{ now()->format('Y/m/d H:i') }}</span>
        <span>{{ $po->po_number }} | {{ config('app.name') }}</span>
    </div>
</div>
</body>
</html>
