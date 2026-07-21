@extends('layouts.master')
@section('title', 'موجودی انبار')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- آمار کلی --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-medium text-muted">کل اقلام</span>
                            <h3 class="mb-0 mt-1">{{ $stats['total_products'] }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2"><i class="bx bx-box bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-medium text-muted">تعداد انبارها</span>
                            <h3 class="mb-0 mt-1">{{ $stats['total_warehouses'] }}</h3>
                        </div>
                        <span class="badge bg-label-info rounded p-2"><i class="bx bx-buildings bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-medium text-muted">زیر حداقل موجودی</span>
                            <h3 class="mb-0 mt-1 text-danger">{{ $stats['below_minimum'] }}</h3>
                        </div>
                        <span class="badge bg-label-danger rounded p-2"><i class="bx bx-error bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="fw-medium text-muted">موجودی صفر / منفی</span>
                            <h3 class="mb-0 mt-1 text-warning">{{ $stats['zero_stock'] }}</h3>
                        </div>
                        <span class="badge bg-label-warning rounded p-2"><i class="bx bx-minus-circle bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.inventory.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجو (کالا / SKU)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="نام کالا یا SKU..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">انبار</label>
                    <select name="warehouse_id" class="form-select">
                        <option value="">همه انبارها</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">وضعیت موجودی</label>
                    <select name="alert" class="form-select">
                        <option value="">همه</option>
                        <option value="1" @selected(request('alert') == '1')>زیر حداقل موجودی</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-filter-alt me-1"></i> اعمال</button>
                </div>
            </form>
        </div>
    </div>

    {{-- جدول موجودی --}}
    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0">
                <i class="bx bx-spreadsheet me-1"></i> موجودی لحظه‌ای انبار
                <small class="text-muted ms-2">({{ $stockList->count() }} قلم)</small>
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('warehouse.inventory.below-minimum') }}" class="btn btn-sm btn-outline-danger">
                    <i class="bx bx-error me-1"></i> زیر حداقل
                </a>
                @can('access', 'stock-transactions.create')
                <a href="{{ route('warehouse.stock-transactions.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> تراکنش جدید
                </a>
                @endcan
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>کالا</th>
                        <th>SKU</th>
                        <th>انبار</th>
                        <th class="text-end">موجودی فعلی</th>
                        <th class="text-center">وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockList as $i => $row)
                    @php
                        $product = \App\Models\Product::find($row->product_id);
                        $qty = (float)$row->quantity;
                        $min = (float)($product->minimum_stock ?? 0);
                        $max = $product ? (float)($product->maximum_stock ?? PHP_INT_MAX) : PHP_INT_MAX;
                        $statusClass = $qty <= 0 ? 'danger' : ($qty < $min ? 'warning' : ($max < PHP_INT_MAX && $qty > $max ? 'info' : 'success'));
                        $statusLabel = $qty <= 0 ? 'صفر/منفی' : ($qty < $min ? 'زیر حداقل' : ($max < PHP_INT_MAX && $qty > $max ? 'بالای حداکثر' : 'مطلوب'));
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-medium">{{ $row->product_title }}</td>
                        <td><small class="text-muted">{{ $row->sku ?? '—' }}</small></td>
                        <td>{{ $row->warehouse_title ?? '—' }}</td>
                        <td class="text-end fw-bold {{ $qty < $min ? 'text-danger' : ($qty <= 0 ? 'text-warning' : '') }}">
                            {{ number_format($qty, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-label-{{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('warehouse.inventory.ledger', $row->product_id) }}?warehouse_id={{ $row->warehouse_id }}"
                               class="btn btn-sm btn-icon btn-outline-primary" title="کارتکس">
                                <i class="bx bx-list-ul"></i>
                            </a>
                            <a href="{{ route('warehouse.inventory.product-stock', $row->product_id) }}"
                               class="btn btn-sm btn-icon btn-outline-info" title="موجودی به تفکیک انبار">
                                <i class="bx bx-buildings"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">هیچ موجودی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
