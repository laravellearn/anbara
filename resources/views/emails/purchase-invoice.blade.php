<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>فاکتور خرید — {{ config('app.name') }}</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; font-size: 12px; color: #222; direction: rtl; background: #f4f5f7; margin: 0; padding: 20px; }
  .wrapper { max-width: 650px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header  { background: #2d3a5e; padding: 24px 32px; color: #fff; }
  .header h1 { margin: 0; font-size: 20px; }
  .header p  { margin: 4px 0 0; font-size: 12px; opacity: .8; }
  .body    { padding: 24px 32px; }
  .meta-grid { display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
  .meta-box  { flex: 1; min-width: 140px; border: 1px solid #e0e0e0; border-radius: 6px; padding: 10px 14px; }
  .meta-box .lbl { font-size: 10px; color: #888; }
  .meta-box .val { font-size: 14px; font-weight: bold; margin-top: 3px; }
  table { width: 100%; border-collapse: collapse; margin: 16px 0; }
  thead tr { background: #2d3a5e; color: #fff; }
  thead th { padding: 7px 10px; font-size: 11px; text-align: right; }
  tbody td { padding: 6px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
  tbody tr:last-child td { border-bottom: none; }
  .total-box { background: #f8f9fa; border-radius: 6px; padding: 14px 20px; text-align: left; margin-top: 8px; }
  .total-box .row { display: flex; justify-content: space-between; margin: 4px 0; font-size: 12px; }
  .total-box .grand { font-size: 16px; font-weight: bold; color: #2d3a5e; border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px; }
  .btn { display: inline-block; background: #4361ee; color: #fff !important; padding: 10px 28px; border-radius: 6px; text-decoration: none; font-size: 13px; margin-top: 16px; }
  .footer { background: #f8f9fa; padding: 14px 32px; text-align: center; font-size: 11px; color: #aaa; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>فاکتور خرید #{{ $invoice->invoice_number }}</h1>
    <p>{{ config('app.name') }} — تأیید و ثبت رسمی فاکتور</p>
  </div>

  <div class="body">
    <div class="meta-grid">
      <div class="meta-box">
        <div class="lbl">شماره فاکتور</div>
        <div class="val">{{ $invoice->invoice_number }}</div>
      </div>
      <div class="meta-box">
        <div class="lbl">تاریخ فاکتور</div>
        <div class="val">{{ verta($invoice->invoice_date)->format('Y/m/d') }}</div>
      </div>
      <div class="meta-box">
        <div class="lbl">تأمین‌کننده</div>
        <div class="val">{{ $invoice->supplier?->name ?? '—' }}</div>
      </div>
      <div class="meta-box">
        <div class="lbl">وضعیت</div>
        <div class="val" style="color:#27ae60;">ثبت شده</div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>کالا</th>
          <th>تعداد</th>
          <th>واحد</th>
          <th>قیمت واحد</th>
          <th>جمع</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoice->items as $i => $item)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $item->product?->title ?? '—' }}</td>
          <td>{{ number_format($item->quantity, 2) }}</td>
          <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
          <td>{{ number_format($item->unit_price) }}</td>
          <td>{{ number_format($item->line_total) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="total-box">
      @if($invoice->discount_percent > 0)
      <div class="row"><span>تخفیف ({{ $invoice->discount_percent }}%)</span><span>{{ number_format($invoice->discount_amount ?? 0) }} ﷼</span></div>
      @endif
      @if($invoice->tax_percent > 0)
      <div class="row"><span>مالیات ({{ $invoice->tax_percent }}%)</span><span>{{ number_format($invoice->tax_amount ?? 0) }} ﷼</span></div>
      @endif
      <div class="row grand"><span>جمع کل قابل پرداخت</span><span>{{ number_format($invoice->grand_total ?? $invoice->items->sum('line_total')) }} ﷼</span></div>
    </div>

    <p style="text-align:center">
      <a href="{{ route('warehouse.purchase-invoices.show', $invoice) }}" class="btn">مشاهده جزئیات فاکتور</a>
    </p>

    @if($invoice->notes)
    <p style="font-size:11px; color:#666; margin-top:16px;"><strong>یادداشت:</strong> {{ $invoice->notes }}</p>
    @endif
  </div>

  <div class="footer">
    این ایمیل به صورت خودکار ارسال شده — {{ config('app.name') }}<br>
    تاریخ ارسال: {{ verta(now())->format('Y/m/d H:i') }}
  </div>
</div>
</body>
</html>
