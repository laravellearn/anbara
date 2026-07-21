@extends('layouts.master')
@section('title', 'ثبت دریافت — ' . $po->po_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bx bx-import me-2"></i> ثبت دریافت — {{ $po->po_number }}</h4>
        <a href="{{ route('warehouse.purchase-orders.show', $po) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> بازگشت
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="alert alert-info">
        <i class="bx bx-info-circle me-1"></i>
        پس از ثبت دریافت، یک <strong>سند رسید انبار</strong> به صورت خودکار ایجاد و تأیید می‌شود.
        مقادیر دریافت‌شده با موجودی انبار به‌روزرسانی خواهند شد.
    </div>

    <form method="POST" action="{{ route('warehouse.purchase-orders.receive', $po) }}">
        @csrf
        <div class="card shadow-none border">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">اقلام دریافتی</h6></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>کالا</th>
                            <th>واحد</th>
                            <th class="text-end">سفارش</th>
                            <th class="text-end">دریافت قبلی</th>
                            <th class="text-end">مانده</th>
                            <th style="width:160px">مقدار دریافت اکنون</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($po->items as $item)
                        <input type="hidden" name="items[{{ $loop->index }}][item_id]" value="{{ $item->id }}">
                        <tr class="{{ $item->isFullyReceived() ? 'table-light text-muted' : '' }}">
                            <td class="fw-medium">{{ $item->product?->title }}</td>
                            <td><small>{{ $item->measurementUnit?->title ?? '—' }}</small></td>
                            <td class="text-end">{{ number_format($item->quantity_ordered, 2) }}</td>
                            <td class="text-end text-success">{{ number_format($item->quantity_received, 2) }}</td>
                            <td class="text-end {{ $item->remaining_qty > 0 ? 'text-warning fw-bold' : 'text-success' }}">
                                {{ number_format($item->remaining_qty, 2) }}
                            </td>
                            <td>
                                @if($item->isFullyReceived())
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control form-control-sm"
                                           value="0" min="0" step="0.0001" disabled>
                                    <small class="text-success">دریافت کامل</small>
                                @else
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control form-control-sm"
                                           value="{{ $item->remaining_qty }}" min="0"
                                           max="{{ $item->remaining_qty }}" step="0.0001">
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-success">
                <i class="bx bx-check me-1"></i> تأیید دریافت و ثبت رسید انبار
            </button>
            <a href="{{ route('warehouse.purchase-orders.show', $po) }}" class="btn btn-outline-secondary">انصراف</a>
        </div>
    </form>
</div>
@endsection
