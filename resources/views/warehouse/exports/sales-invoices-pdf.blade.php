<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>گزارش فاکتورهای فروش</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; font-size: 11px; direction: rtl; }
  h2   { text-align: center; margin-bottom: 10px; font-size: 14px; }
  p.meta { text-align: center; color: #666; margin-bottom: 15px; font-size: 10px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #ccc; padding: 5px 7px; text-align: right; }
  th { background: #f0f0f0; font-size: 11px; }
  tr:nth-child(even) td { background: #fafafa; }
  .total-row td { font-weight: bold; background: #e8f4fd; }
</style>
</head>
<body>
<h2>گزارش فاکتورهای فروش</h2>
<p class="meta">تاریخ تهیه: {{ now()->format('Y-m-d H:i') }} | تعداد: {{ count($invoices) }} فاکتور</p>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>شماره فاکتور</th>
      <th>تاریخ</th>
      <th>مشتری</th>
      <th>جمع کل (ریال)</th>
      <th>پرداخت شده (ریال)</th>
      <th>مانده (ریال)</th>
      <th>وضعیت</th>
    </tr>
  </thead>
  <tbody>
    @foreach($invoices as $i => $inv)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $inv->invoice_number }}</td>
      <td>{{ $inv->invoice_date?->format('Y-m-d') }}</td>
      <td>{{ $inv->customer?->name ?? '—' }}</td>
      <td>{{ number_format($inv->total_amount ?? 0) }}</td>
      <td>{{ number_format($inv->paid_amount ?? 0) }}</td>
      <td>{{ number_format(($inv->total_amount ?? 0) - ($inv->paid_amount ?? 0)) }}</td>
      <td>{{ $inv->status }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr class="total-row">
      <td colspan="4">جمع کل</td>
      <td>{{ number_format($invoices->sum('total_amount')) }}</td>
      <td>{{ number_format($invoices->sum('paid_amount')) }}</td>
      <td>{{ number_format($invoices->sum(fn($x) => $x->total_amount - $x->paid_amount)) }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>
</body>
</html>
