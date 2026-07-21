@extends('layouts.master')
@section('title', 'کالاهای زیر حداقل موجودی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-danger">
                <i class="bx bx-error me-1"></i> کالاهای زیر حداقل موجودی
                <span class="badge bg-danger ms-2">{{ $items->count() }}</span>
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
                        <th>کالا</th>
                        <th>SKU</th>
                        <th>انبار</th>
                        <th class="text-end">موجودی فعلی</th>
                        <th class="text-end">حداقل مجاز</th>
                        <th class="text-end">کمبود</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $row)
                    @php $shortage = (float)$row->minimum_stock - (float)$row->current_stock; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-medium">{{ $row->product_title }}</td>
                        <td><small class="text-muted">{{ $row->sku ?? '—' }}</small></td>
                        <td>{{ $row->warehouse_title ?? '—' }}</td>
                        <td class="text-end text-danger fw-bold">{{ number_format((float)$row->current_stock, 2) }}</td>
                        <td class="text-end">{{ number_format((float)$row->minimum_stock, 2) }}</td>
                        <td class="text-end text-warning fw-bold">{{ number_format($shortage, 2) }}</td>
                        <td class="text-center">
                            <a href="{{ route('warehouse.inventory.ledger', $row->product_id) }}"
                               class="btn btn-sm btn-outline-primary" title="کارتکس">
                                <i class="bx bx-list-ul me-1"></i> کارتکس
                            </a>
                            @can('access', 'stock-transactions.create')
                            <a href="{{ route('warehouse.stock-transactions.create') }}?product_id={{ $row->product_id }}&warehouse_id={{ $row->warehouse_id }}"
                               class="btn btn-sm btn-outline-success" title="ثبت رسید">
                                <i class="bx bx-plus me-1"></i> رسید
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-success py-5">
                        <i class="bx bx-check-circle fs-3 me-2"></i> همه کالاها موجودی کافی دارند.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
