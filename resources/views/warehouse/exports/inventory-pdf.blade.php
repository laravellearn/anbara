<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>گزارش موجودی انبار</title>
<style>
  body { font-family: Tahoma, Arial, sans-serif; font-size: 11px; direction: rtl; }
  h2   { text-align: center; margin-bottom: 10px; font-size: 14px; }
  p.meta { text-align: center; color: #666; margin-bottom: 15px; font-size: 10px; }
  table { width: 100%; border-collapse: collapse; }
  th, td { border: 1px solid #ccc; padding: 5px 7px; text-align: right; }
  th { background: #f0f0f0; }
  tr:nth-child(even) td { background: #fafafa; }
  .low-stock td { background: #fff3cd; color: #856404; }
</style>
</head>
<body>
<h2>گزارش موجودی انبار</h2>
<p class="meta">تاریخ تهیه: {{ now()->format('Y-m-d H:i') }} | تعداد اقلام: {{ count($rows) }}</p>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>کد کالا</th>
      <th>نام کالا</th>
      <th>انبار</th>
      <th>موجودی</th>
      <th>واحد</th>
      <th>حداقل موجودی</th>
    </tr>
  </thead>
  <tbody>
    @foreach($rows as $i => $row)
    <tr class="{{ ($row->quantity <= $row->minimum_stock) ? 'low-stock' : '' }}">
      <td>{{ $i + 1 }}</td>
      <td>{{ $row->sku ?? '—' }}</td>
      <td>{{ $row->product_title }}</td>
      <td>{{ $row->warehouse_title ?? '—' }}</td>
      <td>{{ number_format($row->quantity, 2) }}</td>
      <td>{{ $row->unit_title ?? '—' }}</td>
      <td>{{ number_format($row->minimum_stock ?? 0) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
</body>
</html>
