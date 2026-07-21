@extends('layouts.master')
@section('title', 'خلاصه ورود و خروج')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- KPI cards --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">جمع ورودی (مقدار)</span><h3 class="mb-0 mt-1 text-success">{{ number_format($totals['total_in'], 2) }}</h3></div>
                    <span class="badge bg-label-success rounded p-2"><i class="bx bx-arrow-from-left bx-sm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">جمع خروجی (مقدار)</span><h3 class="mb-0 mt-1 text-danger">{{ number_format($totals['total_out'], 2) }}</h3></div>
                    <span class="badge bg-label-danger rounded p-2"><i class="bx bx-arrow-from-right bx-sm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">ارزش ورودی (ریال)</span><h3 class="mb-0 mt-1 text-success" style="font-size:1.1rem">{{ number_format($totals['value_in']) }}</h3></div>
                    <span class="badge bg-label-success rounded p-2"><i class="bx bx-dollar-circle bx-sm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">ارزش خروجی (ریال)</span><h3 class="mb-0 mt-1 text-danger" style="font-size:1.1rem">{{ number_format($totals['value_out']) }}</h3></div>
                    <span class="badge bg-label-danger rounded p-2"><i class="bx bx-dollar-circle bx-sm"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- فیلترها --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.reports.in-out-summary') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label fw-medium">از تاریخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">تا تاریخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">انبار</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">همه</option>
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
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> اعمال</button>
                        <a href="{{ route('warehouse.reports.in-out-summary') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
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
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">
                <i class="bx bx-transfer-alt me-1"></i> خلاصه ورود و خروج
                <small class="text-muted ms-2">{{ $dateFrom }} تا {{ $dateTo }}</small>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کالا</th>
                        <th>کد</th>
                        <th>دسته</th>
                        <th>واحد</th>
                        <th class="text-end text-success">ورودی</th>
                        <th class="text-end text-danger">خروجی</th>
                        <th class="text-end text-success">ارزش ورودی</th>
                        <th class="text-end text-danger">ارزش خروجی</th>
                        <th>کارتکس</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                    <tr>
                        <td class="fw-medium">{{ $row->product_title }}</td>
                        <td><small class="text-muted">{{ $row->sku ?? '—' }}</small></td>
                        <td><small>{{ $row->category ?? '—' }}</small></td>
                        <td><small>{{ $row->unit ?? '—' }}</small></td>
                        <td class="text-end text-success fw-medium">{{ number_format($row->total_in, 2) }}</td>
                        <td class="text-end text-danger fw-medium">{{ number_format($row->total_out, 2) }}</td>
                        <td class="text-end">{{ number_format($row->value_in) }} ﷼</td>
                        <td class="text-end">{{ number_format($row->value_out) }} ﷼</td>
                        <td>
                            <a href="{{ route('warehouse.reports.ledger', ['product_id' => $row->product_id, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                               class="btn btn-sm btn-icon btn-outline-info" title="کارتکس"><i class="bx bx-list-ul"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">داده‌ای یافت نشد.</td></tr>
                    @endforelse
                </tbody>
                @if($rows->count())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4">جمع کل</td>
                        <td class="text-end text-success">{{ number_format($totals['total_in'], 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($totals['total_out'], 2) }}</td>
                        <td class="text-end text-success">{{ number_format($totals['value_in']) }} ﷼</td>
                        <td class="text-end text-danger">{{ number_format($totals['value_out']) }} ﷼</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
