<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>اصالت‌سنجی سند — {{ config('app.name') }}</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: Tahoma, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; direction: rtl; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .card { background: #fff; border-radius: 12px; padding: 40px; max-width: 520px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,.1); text-align: center; }
  .icon { font-size: 64px; margin-bottom: 16px; }
  h1 { font-size: 22px; margin: 0 0 8px; }
  .sub { font-size: 13px; color: #666; margin-bottom: 24px; }
  .info-table { width: 100%; border-collapse: collapse; text-align: right; margin: 20px 0; }
  .info-table td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
  .info-table td:first-child { color: #888; width: 140px; font-weight: bold; }
  .badge-valid   { display: inline-block; background: #d4edda; color: #155724; padding: 6px 20px; border-radius: 20px; font-size: 13px; font-weight: bold; }
  .badge-invalid { display: inline-block; background: #f8d7da; color: #721c24; padding: 6px 20px; border-radius: 20px; font-size: 13px; font-weight: bold; }
  .footer { margin-top: 24px; font-size: 11px; color: #aaa; }
  .uuid { font-family: monospace; font-size: 11px; color: #999; word-break: break-all; margin-top: 8px; }
</style>
</head>
<body>
<div class="card">
  @if($valid)
    <div class="icon">✅</div>
    <h1 style="color:#155724;">سند معتبر است</h1>
    <p class="sub">اصالت این سند تأیید شد. اطلاعات زیر صحیح و دستکاری‌نشده است.</p>
    <span class="badge-valid">✔ تأیید اصالت</span>
  @else
    <div class="icon">❌</div>
    <h1 style="color:#721c24;">سند نامعتبر است</h1>
    <p class="sub">امضای این سند با اطلاعات موجود مطابقت ندارد. احتمالاً دستکاری شده است.</p>
    <span class="badge-invalid">✗ عدم تأیید اصالت</span>
  @endif

  <table class="info-table">
    @if($type === 'warehouse_document')
      <tr><td>نوع سند</td><td>سند انبار</td></tr>
      <tr><td>شماره سند</td><td>{{ $record->document_number }}</td></tr>
      <tr><td>تاریخ سند</td><td>{{ verta($record->document_date)->format('Y/m/d') }}</td></tr>
      <tr><td>نوع عملیات</td><td>{{ $record->type_label ?? $record->type }}</td></tr>
      <tr><td>وضعیت</td><td>{{ $record->status_label ?? $record->status }}</td></tr>
    @else
      <tr><td>نوع سند</td><td>فاکتور خرید</td></tr>
      <tr><td>شماره فاکتور</td><td>{{ $record->invoice_number }}</td></tr>
      <tr><td>تاریخ فاکتور</td><td>{{ verta($record->invoice_date)->format('Y/m/d') }}</td></tr>
      <tr><td>تأمین‌کننده</td><td>{{ $record->supplier?->name ?? '—' }}</td></tr>
      <tr><td>وضعیت</td><td>{{ $record->status }}</td></tr>
    @endif
    @if($record->signed_at)
      <tr><td>تاریخ امضا</td><td>{{ verta($record->signed_at)->format('Y/m/d H:i') }}</td></tr>
    @endif
  </table>

  <div class="uuid">UUID: {{ $uuid }}</div>
  <div class="footer">{{ config('app.name') }} — سیستم اصالت‌سنجی اسناد</div>
</div>
</body>
</html>
