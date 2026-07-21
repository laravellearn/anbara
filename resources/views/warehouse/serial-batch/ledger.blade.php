@extends('warehouse.serial-batch.index')
{{--
  Ledger view re-uses the index layout but focuses on one product.
  Stand-alone simple view:
--}}
@extends('layouts.app')
@section('title', 'ردیابی: '.$product->title)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="fw-bold mb-0">ردیابی سریال/بچ — {{ $product->title }}</h4>
    <a href="{{ route('warehouse.serial-batch.index') }}" class="btn btn-outline-secondary btn-sm">بازگشت</a>
  </div>
  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle table-hover">
        <thead class="table-light">
          <tr><th>شماره</th><th>نوع</th><th>انبار</th><th>سند مرتبط</th><th>تاریخ تولید</th><th>تاریخ انقضا</th><th class="text-end">مقدار</th><th>وضعیت</th><th>تاریخ ثبت</th></tr>
        </thead>
        <tbody>
          @forelse($items as $item)
          @php $sc=\App\Models\SerialBatch::statusColors();$sl=\App\Models\SerialBatch::statusLabels(); @endphp
          <tr class="{{ $item->expiry_date && $item->expiry_date->isPast() && $item->status==='in_stock'?'table-danger':'' }}">
            <td><code>{{ $item->serial_number ?? $item->batch_number }}</code></td>
            <td><span class="badge bg-label-{{ $item->tracking_type==='serial'?'info':'primary' }}">{{ $item->tracking_type==='serial'?'سریال':'بچ' }}</span></td>
            <td>{{ $item->warehouse?->title ?? '—' }}</td>
            <td>{{ $item->warehouseDocument?->document_number ?? '—' }}</td>
            <td>{{ $item->manufacture_date?->format('Y-m-d') ?? '—' }}</td>
            <td>
              @if($item->expiry_date)
                <span class="{{ $item->expiry_date->isPast()?'text-danger fw-bold':($item->expiry_date->diffInDays(now())<=30?'text-warning':'') }}">
                  {{ $item->expiry_date->format('Y-m-d') }}
                </span>
              @else —
              @endif
            </td>
            <td class="text-end">{{ number_format($item->quantity,2) }}</td>
            <td><span class="badge bg-label-{{ $sc[$item->status]??'secondary' }}">{{ $sl[$item->status]??$item->status }}</span></td>
            <td>{{ $item->created_at->format('Y-m-d') }}</td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-4">رکوردی یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($items->hasPages())<div class="card-footer">{{ $items->links() }}</div>@endif
  </div>
</div>
@endsection
