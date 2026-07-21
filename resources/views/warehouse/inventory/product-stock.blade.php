@extends('layouts.master')
@section('title', 'موجودی به تفکیک انبار — ' . $product->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0">{{ $product->title }}</h5>
                <small class="text-muted">SKU: {{ $product->sku ?? '—' }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('warehouse.inventory.ledger', $product) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-list-ul me-1"></i> کارتکس
                </a>
                <a href="{{ route('warehouse.inventory.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> بازگشت
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card bg-label-primary border-0 text-center p-3">
                        <div class="text-muted mb-1">موجودی کل</div>
                        <h2 class="mb-0">{{ number_format($totalStock, 2) }}</h2>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>انبار</th>
                            <th class="text-end">موجودی</th>
                            <th class="text-center">وضعیت</th>
                            <th class="text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockByWarehouse as $i => $row)
                        @php
                            $qty = (float)$row->quantity;
                            $min = (float)($product->minimum_stock ?? 0);
                            $status = $qty <= 0 ? ['label'=>'صفر','color'=>'danger']
                                    : ($qty < $min ? ['label'=>'زیر حداقل','color'=>'warning']
                                    : ['label'=>'مطلوب','color'=>'success']);
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-medium">{{ $row->warehouse_title }}</td>
                            <td class="text-end fw-bold {{ $qty < $min ? 'text-danger' : '' }}">
                                {{ number_format($qty, 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $status['color'] }}">{{ $status['label'] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('warehouse.inventory.ledger', $product) }}?warehouse_id={{ $row->warehouse_id }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-list-ul me-1"></i> کارتکس این انبار
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">موجودی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
