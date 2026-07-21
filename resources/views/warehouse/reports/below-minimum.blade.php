@extends('layouts.master')
@section('title', 'کالاهای زیر حداقل موجودی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- هشدار --}}
    @if($rows->count())
    <div class="alert alert-danger alert-dismissible fade show mb-4">
        <i class="bx bx-error-circle me-1"></i>
        <strong>هشدار:</strong> {{ $rows->count() }} کالا زیر حداقل موجودی هستند و نیاز به تأمین دارند.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @else
    <div class="alert alert-success mb-4">
        <i class="bx bx-check-circle me-1"></i> همه کالاها بالاتر از حداقل موجودی هستند.
    </div>
    @endif

    {{-- فیلتر --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.reports.below-minimum') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">انبار</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">همه انبارها</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-filter me-1"></i> فیلتر</button>
                        <a href="{{ route('warehouse.reports.below-minimum') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success w-100">
                            <i class="bx bx-download me-1"></i> Excel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-danger">
                <i class="bx bx-error me-1"></i> کالاهای زیر حداقل موجودی ({{ $rows->count() }})
            </h5>
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
                        <th class="text-end">حداقل مجاز</th>
                        <th class="text-end text-danger">کسری</th>
                        <th>کارتکس</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    @php $deficit = $row->minimum_stock - $row->current_stock; @endphp
                    <tr>
                        <td class="fw-medium">{{ $row->product_title }}</td>
                        <td><small class="text-muted">{{ $row->sku ?? '—' }}</small></td>
                        <td><small>{{ $row->category ?? '—' }}</small></td>
                        <td>{{ $row->warehouse_title }}</td>
                        <td><small>{{ $row->unit ?? '—' }}</small></td>
                        <td class="text-end {{ $row->current_stock <= 0 ? 'text-danger fw-bold' : 'text-warning fw-bold' }}">
                            {{ number_format($row->current_stock, 2) }}
                        </td>
                        <td class="text-end">{{ number_format($row->minimum_stock, 2) }}</td>
                        <td class="text-end text-danger fw-bold">{{ number_format($deficit, 2) }}</td>
                        <td>
                            <a href="{{ route('warehouse.reports.ledger', ['product_id' => $row->product_id, 'warehouse_id' => $row->warehouse_id]) }}"
                               class="btn btn-sm btn-icon btn-outline-info" title="کارتکس"><i class="bx bx-list-ul"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">همه موجودی‌ها بالاتر از حداقل هستند.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
