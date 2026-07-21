<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>چاپ سند انبار — {{ $doc->document_number }}</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;600;700&display=swap');
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Vazirmatn', Tahoma, sans-serif; font-size: 12px; color: #1a1a1a; background: #fff; direction: rtl; }
  .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm 15mm 20mm; }
  .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #059669; padding-bottom: 10px; margin-bottom: 16px; }
  .company-info h1 { font-size: 18px; font-weight: 700; color: #059669; margin-bottom: 4px; }
  .doc-number { font-size: 22px; font-weight: 700; color: #059669; }
  .badge-status { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; margin-top: 4px; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
  .info-box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 12px; }
  .info-box h4 { font-size: 10px; color: #888; font-weight: 600; text-transform: uppercase; margin-bottom: 6px; border-bottom: 1px solid #f0f0f0; padding-bottom: 4px; }
  .info-row { display: flex; justify-content: space-between; margin-bottom: 3px; }
  .info-row .lbl { color: #666; }
  .info-row .val { font-weight: 600; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  thead th { background: #059669; color: #fff; padding: 7px 8px; text-align: right; font-size: 11px; }
  tbody tr:nth-child(even) { background: #f0fdf4; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
  tfoot td { padding: 6px 8px; font-weight: 600; background: #f9fafb; }
  .text-end { text-align: left; }
  .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 30px; }
  .sig-box { border-top: 2px solid #ccc; padding-top: 8px; text-align: center; }
  .sig-box .sig-label { font-size: 10px; color: #888; }
  .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 6px; display: flex; justify-content: space-between; font-size: 9px; color: #aaa; }
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .no-print { display: none !important; }
    .page { padding: 10mm; }
  }
</style>
</head>
<body>

<div class="no-print" style="text-align:center;padding:12px;background:#ecfdf5;border-bottom:1px solid #a7f3d0">
    <button onclick="window.print()" style="background:#059669;color:#fff;border:none;padding:8px 24px;border-radius:6px;cursor:pointer;font-size:13px;font-family:Tahoma">
        🖨️ چاپ / ذخیره PDF
    </button>
    <button onclick="history.back()" style="background:#6b7280;color:#fff;border:none;padding:8px 18px;border-radius:6px;cursor:pointer;font-size:13px;margin-right:8px;font-family:Tahoma">
        بازگشت
    </button>
</div>

<div class="page">
    <div class="header">
        <div class="company-info">
            @php
                $tenantId   = app(\App\Services\TenantManager::class)->getTenantId();
                $orgName    = \App\Models\TenantSetting::get($tenantId, 'org_name', config('app.name'));
                $orgLogo    = \App\Models\TenantSetting::get($tenantId, 'org_logo', '');
                $brandColor = \App\Models\TenantSetting::get($tenantId, 'org_brand_color', '#059669');
                $orgPhone   = \App\Models\TenantSetting::get($tenantId, 'org_phone', '');
            @endphp
            @if($orgLogo)
                <img src="{{ asset('storage/'.$orgLogo) }}" style="max-height:55px;max-width:140px;margin-bottom:6px;display:block" alt="{{ $orgName }}">
            @endif
            <h1 style="color:{{ $brandColor }}">{{ $orgName }}</h1>
            @if($orgPhone)<p style="font-size:10px;color:#555">📞 {{ $orgPhone }}</p>@endif
            <p>{{ $doc->type_label ?? $doc->type }}</p>
        </div>
        <div style="text-align:left">
            <div style="font-size:10px;color:#888">شماره سند</div>
            <div class="doc-number">{{ $doc->document_number }}</div>
            <span class="badge-status" style="background:#d1fae5;color:#065f46">{{ $doc->status_label ?? $doc->status }}</span>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h4>اطلاعات سند</h4>
            <div class="info-row"><span class="lbl">نوع سند:</span><span class="val">{{ $doc->type_label ?? $doc->type }}</span></div>
            <div class="info-row"><span class="lbl">تاریخ:</span><span class="val">{{ $doc->document_date ?? $doc->created_at?->format('Y/m/d') }}</span></div>
            <div class="info-row"><span class="lbl">انبار:</span><span class="val">{{ $doc->warehouse?->title }}</span></div>
            @if($doc->source_warehouse_id)
            <div class="info-row"><span class="lbl">انبار مبدأ:</span><span class="val">{{ $doc->sourceWarehouse?->title }}</span></div>
            @endif
            <div class="info-row"><span class="lbl">مرجع:</span><span class="val">{{ $doc->reference_number ?? '—' }}</span></div>
        </div>
        <div class="info-box">
            <h4>مالی و سازمانی</h4>
            <div class="info-row"><span class="lbl">طرف حساب:</span><span class="val">{{ $doc->contact?->name ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">مرکز هزینه:</span><span class="val">{{ $doc->costCenter?->title ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">سال مالی:</span><span class="val">{{ $doc->fiscalYear?->title ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">ثبت‌کننده:</span><span class="val">{{ $doc->creator?->name ?? '—' }}</span></div>
        </div>
    </div>

    @if($doc->description)
    <div style="border:1px solid #e5e7eb;border-radius:6px;padding:8px 12px;margin-bottom:14px;font-size:11px;color:#555">
        <strong>توضیحات:</strong> {{ $doc->description }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>شرح کالا</th>
                <th>کد</th>
                <th>واحد</th>
                <th class="text-end">مقدار</th>
                <th class="text-end">قیمت واحد</th>
                <th class="text-end">ارزش</th>
                <th>سریال / انقضا</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc->items as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $item->product?->title }}<br>@if($item->description)<small style="color:#888">{{ $item->description }}</small>@endif</td>
                <td><small>{{ $item->product?->sku ?? '—' }}</small></td>
                <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-end">{{ $item->unit_price ? number_format($item->unit_price) : '—' }}</td>
                <td class="text-end">{{ $item->unit_price ? number_format($item->quantity * $item->unit_price) : '—' }}</td>
                <td><small>{{ $item->serial_number ?? '' }} {{ $item->expiry_date ? '| '.$item->expiry_date : '' }}</small></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end">جمع:</td>
                <td class="text-end">{{ number_format($doc->items->sum('quantity'), 2) }}</td>
                <td></td>
                <td class="text-end">{{ number_format($doc->items->sum(fn($i) => $i->quantity * ($i->unit_price ?? 0))) }} ﷼</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div class="sig-box"><div class="sig-label">صادرکننده<br><small>{{ $doc->creator?->name }}</small></div></div>
        <div class="sig-box"><div class="sig-label">تأییدکننده<br><small>{{ $doc->approver?->name ?? '—' }}</small></div></div>
        <div class="sig-box"><div class="sig-label">انبارداری</div></div>
    </div>

    <div class="footer">
        <span>تاریخ چاپ: {{ now()->format('Y/m/d H:i') }}</span>
        <span>{{ $doc->document_number }} | {{ config('app.name') }}</span>
    </div>
</div>
</body>
</html>
