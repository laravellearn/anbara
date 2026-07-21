@extends('layouts.master')
@section('title', 'جزئیات تراکنش')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">
                <i class="bx bx-transfer me-1"></i>
                تراکنش #{{ $stockTransaction->id }}
                <span class="badge bg-label-{{ $stockTransaction->status->color() }} ms-2">{{ $stockTransaction->status->label() }}</span>
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                {{-- ارسال برای تأیید --}}
                @can('access', 'stock-transactions.submit')
                @if($stockTransaction->status->value === 'draft')
                <form action="{{ route('warehouse.stock-transactions.submit', $stockTransaction) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-info"><i class="bx bx-send me-1"></i> ارسال برای تأیید</button>
                </form>
                @endif
                @endcan
                {{-- تأیید / رد --}}
                @can('access', 'stock-transactions.approve')
                @if($stockTransaction->status->value === 'pending')
                <form action="{{ route('warehouse.stock-transactions.approve', $stockTransaction) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-success"><i class="bx bx-check me-1"></i> تأیید</button>
                </form>
                <form action="{{ route('warehouse.stock-transactions.reject', $stockTransaction) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-danger"><i class="bx bx-x me-1"></i> رد</button>
                </form>
                @endif
                @endcan
                {{-- ویرایش --}}
                @can('access', 'stock-transactions.edit')
                @if($stockTransaction->status->value === 'draft')
                <a href="{{ route('warehouse.stock-transactions.edit', $stockTransaction) }}" class="btn btn-sm btn-warning">
                    <i class="bx bx-edit me-1"></i> ویرایش
                </a>
                @endif
                @endcan
                <a href="{{ route('warehouse.stock-transactions.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> بازگشت
                </a>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><th class="text-muted" width="40%">نوع تراکنش</th>
                            <td><span class="badge bg-label-{{ $stockTransaction->type->color() }}">{{ $stockTransaction->type->label() }}</span></td></tr>
                        <tr><th class="text-muted">کالا</th>
                            <td>{{ $stockTransaction->product->title ?? '—' }}
                                @if($stockTransaction->product->sku)<small class="text-muted">({{ $stockTransaction->product->sku }})</small>@endif
                            </td></tr>
                        <tr><th class="text-muted">انبار</th><td>{{ $stockTransaction->warehouse->title ?? '—' }}</td></tr>
                        <tr><th class="text-muted">موقعیت</th><td>{{ $stockTransaction->warehouseLocation->title ?? '—' }}</td></tr>
                        <tr><th class="text-muted">مقدار</th>
                            <td class="{{ $stockTransaction->isInbound() ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $stockTransaction->isInbound() ? '+' : '−' }}{{ number_format($stockTransaction->quantity, 4) }}
                                {{ $stockTransaction->measurementUnit->title ?? '' }}
                            </td></tr>
                        <tr><th class="text-muted">قیمت واحد</th><td>{{ $stockTransaction->unit_price ? number_format($stockTransaction->unit_price) . ' ﷼' : '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><th class="text-muted" width="40%">سال مالی</th><td>{{ $stockTransaction->fiscalYear->name ?? '—' }}</td></tr>
                        <tr><th class="text-muted">مرکز هزینه</th><td>{{ $stockTransaction->costCenter->title ?? '—' }}</td></tr>
                        <tr><th class="text-muted">شماره سریال</th><td>{{ $stockTransaction->serial_number ?? '—' }}</td></tr>
                        <tr><th class="text-muted">شماره بچ</th><td>{{ $stockTransaction->batch_number ?? '—' }}</td></tr>
                        <tr><th class="text-muted">تاریخ انقضا</th><td>{{ $stockTransaction->expiry_date ? $stockTransaction->expiry_date->format('Y/m/d') : '—' }}</td></tr>
                        <tr><th class="text-muted">ثبت‌کننده</th><td>{{ $stockTransaction->user->name ?? '—' }}</td></tr>
                        <tr><th class="text-muted">تاریخ ثبت</th><td>{{ $stockTransaction->created_at->format('Y/m/d H:i') }}</td></tr>
                    </table>
                </div>
                @if($stockTransaction->description)
                <div class="col-12">
                    <div class="alert alert-light border">
                        <strong>توضیحات:</strong> {{ $stockTransaction->description }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
