@extends('layouts.master')
@section('title', 'کارتکس کالا — ' . $product->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- اطلاعات کالا --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-1">{{ $product->title }}</h5>
                    <small class="text-muted">SKU: {{ $product->sku ?? '—' }}</small>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-label-primary fs-6 px-3 py-2">
                        موجودی کل: {{ number_format($currentStock, 2) }}
                    </span>
                </div>
            </div>

            {{-- موجودی به تفکیک انبار --}}
            @if($stockByWarehouse->count() > 1)
            <hr>
            <div class="row g-3 mt-1">
                @foreach($stockByWarehouse as $sw)
                <div class="col-auto">
                    <div class="badge bg-label-{{ (float)$sw->quantity > 0 ? 'info' : 'secondary' }} px-3 py-2">
                        {{ $sw->warehouse_title }}: <strong>{{ number_format($sw->quantity, 2) }}</strong>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- فیلتر کارتکس --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.inventory.ledger', $product) }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">انبار</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">همه انبارها</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">از تاریخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">تا تاریخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-filter-alt me-1"></i> اعمال</button>
                </div>
            </form>
        </div>
    </div>

    {{-- جدول کارتکس --}}
    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">
                <i class="bx bx-list-ul me-1"></i> کارتکس کالا
                <small class="text-muted ms-2">({{ $transactions->count() }} ردیف)</small>
            </h5>
            <a href="{{ route('warehouse.inventory.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> بازگشت به موجودی
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>نوع</th>
                        <th>انبار</th>
                        <th class="text-end">ورودی</th>
                        <th class="text-end">خروجی</th>
                        <th class="text-end">تراز</th>
                        <th>توضیحات</th>
                        <th>ثبت‌کننده</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $i => $row)
                    @php
                        $isIn  = (float)$row->net_quantity > 0;
                        $isOut = (float)$row->net_quantity < 0;
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><small>{{ \Carbon\Carbon::parse($row->created_at)->format('Y/m/d H:i') }}</small></td>
                        <td>
                            <span class="badge bg-label-{{ \App\Enums\InventoryTransactionType::from($row->type)->color() }}">
                                {{ \App\Enums\InventoryTransactionType::from($row->type)->label() }}
                            </span>
                        </td>
                        <td>{{ $row->warehouse_title }}</td>
                        <td class="text-end text-success fw-medium">
                            {{ $isIn ? number_format((float)$row->net_quantity, 2) : '—' }}
                        </td>
                        <td class="text-end text-danger fw-medium">
                            {{ $isOut ? number_format(abs((float)$row->net_quantity), 2) : '—' }}
                        </td>
                        <td class="text-end fw-bold {{ (float)$row->balance < 0 ? 'text-danger' : '' }}">
                            {{ number_format((float)$row->balance, 2) }}
                        </td>
                        <td><small class="text-muted">{{ $row->description ?? '—' }}</small></td>
                        <td><small>{{ $row->user_name ?? '—' }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">حرکتی یافت نشد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
