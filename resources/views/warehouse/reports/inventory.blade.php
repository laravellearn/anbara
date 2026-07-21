@extends('layouts.master')
@section('title', 'گزارش موجودی لحظه‌ای')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- خلاصه آماری --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">کل اقلام</span><h3 class="mb-0 mt-1">{{ $rows->total() }}</h3></div>
                    <span class="badge bg-label-primary rounded p-2"><i class="bx bx-package bx-sm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">زیر حداقل</span><h3 class="mb-0 mt-1 text-danger">{{ $summary['below_min_count'] }}</h3></div>
                    <span class="badge bg-label-danger rounded p-2"><i class="bx bx-error bx-sm"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.reports.inventory') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">جستجوی کالا</label>
                        <input type="text" name="product_search" class="form-control" placeholder="نام یا کد کالا..." value="{{ request('product_search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">انبار</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">همه انبارها</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">دسته‌بندی</label>
                        <select name="category_id" class="form-select">
                            <option value="">همه</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">نمایش موجودی صفر</label>
                        <select name="zero_stock" class="form-select">
                            <option value="">نمایش همه</option>
                            <option value="hide" @selected(request('zero_stock') === 'hide')>مخفی کردن</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="below_min" value="1" id="belowMin" @checked(request('below_min') == '1')>
                            <label class="form-check-label" for="belowMin">زیر حداقل</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> اعمال</button>
                        <a href="{{ route('warehouse.reports.inventory') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-layer me-1"></i> موجودی لحظه‌ای</h5>
            <div class="d-flex gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-sm btn-success">
                    <i class="bx bx-download me-1"></i> خروجی Excel
                </a>
                <a href="{{ route('warehouse.reports.inventory.pdf') }}?{{ http_build_query(request()->except('page')) }}" target="_blank" class="btn btn-sm btn-danger">
                    <i class="bx bx-file-pdf me-1"></i> خروجی PDF
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کالا</th>
                        <th>کد</th>
                        <th>دسته</th>
                        <th>انبار</th>
                        <th>واحد</th>
                        <th class="text-end">موجودی جاری</th>
                        <th class="text-end">حداقل</th>
                        <th>وضعیت</th>
                        <th>کارتکس</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    @php
                        $isBelowMin = $row->minimum_stock > 0 && $row->current_stock < $row->minimum_stock;
                        $isZero     = $row->current_stock <= 0;
                    @endphp
                    <tr class="{{ $isBelowMin ? 'table-danger' : '' }}">
                        <td class="fw-medium">{{ $row->product_title }}</td>
                        <td><small class="text-muted">{{ $row->sku ?? '—' }}</small></td>
                        <td><small>{{ $row->category ?? '—' }}</small></td>
                        <td>{{ $row->warehouse_title }}</td>
                        <td><small>{{ $row->unit ?? '—' }}</small></td>
                        <td class="text-end fw-bold {{ $isZero ? 'text-danger' : ($isBelowMin ? 'text-warning' : 'text-success') }}">
                            {{ number_format($row->current_stock, 2) }}
                        </td>
                        <td class="text-end">
                            <small>{{ $row->minimum_stock > 0 ? number_format($row->minimum_stock, 2) : '—' }}</small>
                        </td>
                        <td>
                            @if($isZero)
                                <span class="badge bg-label-danger">اتمام موجودی</span>
                            @elseif($isBelowMin)
                                <span class="badge bg-label-warning">زیر حداقل</span>
                            @else
                                <span class="badge bg-label-success">مطلوب</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('warehouse.reports.ledger', ['product_id' => $row->product_id, 'warehouse_id' => $row->warehouse_id]) }}"
                               class="btn btn-sm btn-icon btn-outline-info" title="کارتکس">
                                <i class="bx bx-list-ul"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">موردی یافت نشد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">نمایش {{ $rows->firstItem() ?? 0 }} تا {{ $rows->lastItem() ?? 0 }} از {{ $rows->total() }}</small>
            {{ $rows->links() }}
        </div>
    </div>
</div>
@endsection
