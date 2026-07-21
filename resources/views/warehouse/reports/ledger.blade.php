@extends('layouts.master')
@section('title', 'کارتکس کالا')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- فرم انتخاب کالا و فیلتر --}}
    <div class="card shadow-none border mb-4">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0"><i class="bx bx-book-open me-1"></i> کارتکس کالا</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('warehouse.reports.ledger') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">کالا <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select" required>
                            <option value="">انتخاب کالا...</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" @selected(request('product_id') == $p->id)>
                                {{ $p->title }}{{ $p->sku ? ' ('.$p->sku.')' : '' }}
                            </option>
                            @endforeach
                        </select>
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
                        <label class="form-label fw-medium">از تاریخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">تا تاریخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> نمایش کارتکس</button>
                        @if($product)
                        <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success"><i class="bx bx-download"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($product)
    {{-- کارت اطلاعات کالا --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h6 class="mb-1 fw-bold">{{ $product->title }}</h6>
                <small class="text-muted">{{ $product->sku ?? '' }}</small>
            </div>
            <div class="d-flex gap-4">
                <div class="text-center">
                    <div class="text-muted small">موجودی افتتاحی</div>
                    <div class="fw-bold fs-5">{{ number_format($openingBal, 2) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-muted small">جمع ورودی</div>
                    <div class="fw-bold fs-5 text-success">{{ number_format($rows->sum('qty_in'), 2) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-muted small">جمع خروجی</div>
                    <div class="fw-bold fs-5 text-danger">{{ number_format($rows->sum('qty_out'), 2) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-muted small">موجودی اختتامی</div>
                    <div class="fw-bold fs-5 text-primary">{{ number_format($closingBal, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول کارتکس --}}
    <div class="card shadow-none border">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>تاریخ</th>
                        <th>نوع</th>
                        <th>انبار</th>
                        <th>توضیحات</th>
                        <th class="text-end text-success">ورودی</th>
                        <th class="text-end text-danger">خروجی</th>
                        <th class="text-end">تراز</th>
                        <th>ثبت‌کننده</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ردیف موجودی افتتاحی --}}
                    <tr class="table-light fw-medium">
                        <td colspan="6">موجودی افتتاحی @if(request('date_from'))(قبل از {{ request('date_from') }})@endif</td>
                        <td class="text-end fw-bold">{{ number_format($openingBal, 2) }}</td>
                        <td></td>
                    </tr>
                    @forelse($rows as $row)
                    <tr>
                        <td><small>{{ \Carbon\Carbon::parse($row->created_at)->format('Y/m/d H:i') }}</small></td>
                        <td>
                            @php
                                $inTypes = ['purchase_receipt','return_from_customer','opening','transfer_in','adjustment_in','receipt','return_in'];
                                $isIn = in_array($row->type, $inTypes);
                            @endphp
                            <span class="badge bg-label-{{ $isIn ? 'success' : 'danger' }}">
                                {{ $isIn ? 'ورودی' : 'خروجی' }}
                            </span>
                        </td>
                        <td><small>{{ $row->warehouse_title }}</small></td>
                        <td><small class="text-muted">{{ $row->description ?? '—' }}</small></td>
                        <td class="text-end text-success fw-medium">
                            {{ $row->qty_in > 0 ? number_format($row->qty_in, 2) : '—' }}
                        </td>
                        <td class="text-end text-danger fw-medium">
                            {{ $row->qty_out > 0 ? number_format($row->qty_out, 2) : '—' }}
                        </td>
                        <td class="text-end fw-bold {{ $row->balance < 0 ? 'text-danger' : 'text-primary' }}">
                            {{ number_format($row->balance, 2) }}
                        </td>
                        <td><small class="text-muted">{{ $row->user_name ?? '—' }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">تراکنشی در این بازه یافت نشد.</td></tr>
                    @endforelse
                    {{-- ردیف اختتامی --}}
                    @if($rows->count())
                    <tr class="table-primary fw-bold">
                        <td colspan="4" class="text-end">موجودی اختتامی</td>
                        <td class="text-end text-success">{{ number_format($rows->sum('qty_in'), 2) }}</td>
                        <td class="text-end text-danger">{{ number_format($rows->sum('qty_out'), 2) }}</td>
                        <td class="text-end text-primary fs-6">{{ number_format($closingBal, 2) }}</td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="alert alert-info"><i class="bx bx-info-circle me-1"></i> ابتدا یک کالا انتخاب کنید تا کارتکس نمایش داده شود.</div>
    @endif
</div>
@endsection
