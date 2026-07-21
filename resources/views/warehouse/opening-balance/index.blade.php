@extends('layouts.master')
@section('title', 'ثبت موجودی اولیه')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- هدر و راهنما --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body d-flex gap-3 align-items-start">
            <span class="badge bg-label-primary rounded p-2 mt-1"><i class="bx bx-info-circle bx-sm"></i></span>
            <div>
                <h6 class="mb-1">راهنمای ثبت موجودی اولیه</h6>
                <p class="mb-0 text-muted small">
                    موجودی اولیه (Opening Balance) برای وارد کردن کالاهایی است که قبل از راه‌اندازی سیستم در انبار موجود بوده‌اند.
                    این تراکنش‌ها به‌صورت خودکار <strong class="text-success">تأیید شده</strong> ثبت می‌شوند و بلافاصله در موجودی لحظه‌ای اثر می‌گذارند.
                    اگر برای یک کالا در یک انبار قبلاً موجودی اولیه ثبت شده باشد، ردیف مجدد نادیده گرفته می‌شود.
                </p>
            </div>
        </div>
    </div>

    @if(session('toast'))
    <div class="alert alert-{{ session('toast.type') === 'success' ? 'success' : 'warning' }} alert-dismissible fade show mb-4">
        <strong>{{ session('toast.title') }}:</strong> {{ session('toast.message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- موجودی‌های قبلاً ثبت‌شده --}}
    @if($existing->count())
    <div class="card shadow-none border mb-4">
        <div class="card-header border-bottom">
            <h6 class="card-title mb-0 text-success">
                <i class="bx bx-check-circle me-1"></i> موجودی‌های اولیه ثبت‌شده ({{ $existing->count() }} کالا)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr><th>کالا</th><th>انبار</th><th class="text-end">مقدار</th><th>واحد</th><th>تاریخ</th></tr>
                </thead>
                <tbody>
                    @foreach($existing as $productId => $txs)
                        @foreach($txs as $tx)
                        <tr>
                            <td>{{ $tx->product->title ?? '—' }}</td>
                            <td>{{ $tx->warehouse->title ?? '—' }}</td>
                            <td class="text-end text-success fw-medium">{{ number_format($tx->quantity, 2) }}</td>
                            <td><small>{{ $tx->measurementUnit->title ?? '' }}</small></td>
                            <td><small class="text-muted">{{ $tx->created_at->format('Y/m/d') }}</small></td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- فرم اصلی --}}
    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-import me-1"></i> ثبت موجودی اولیه جدید</h5>
        </div>

        <form action="{{ route('warehouse.opening-balance.store') }}" method="POST" id="openingForm">
            @csrf
            <div class="card-body">

                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                {{-- انتخاب انبار --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">انبار <span class="text-danger">*</span></label>
                        <select name="warehouse_id" id="warehouseId" class="form-select" required>
                            <option value="">انتخاب انبار...</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(old('warehouse_id') == $wh->id)>{{ $wh->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- جدول اقلام --}}
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40%">کالا <span class="text-danger">*</span></th>
                                <th style="width:20%">مقدار <span class="text-danger">*</span></th>
                                <th style="width:25%">قیمت واحد (ریال)</th>
                                <th style="width:10%" class="text-center">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][product_id]" class="form-select form-select-sm product-select" required>
                                        <option value="">انتخاب کالا...</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }} @if($p->sku)({{ $p->sku }})@endif</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm" step="0.0001" min="0.0001" placeholder="0.00" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_price]" class="form-control form-control-sm" step="1" min="0" placeholder="اختیاری">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row" disabled>
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" id="btnAddRow" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bx bx-plus me-1"></i> اضافه کردن کالا
                </button>
            </div>

            <div class="card-footer text-end d-flex justify-content-between align-items-center">
                <small class="text-muted" id="rowCount">۱ ردیف</small>
                <div>
                    <a href="{{ route('warehouse.inventory.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-save me-1"></i> ثبت موجودی اولیه
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const productOptions = `{!! $products->map(fn($p) => '<option value="'.$p->id.'">'.$p->title.($p->sku ? ' ('.$p->sku.')' : '').'</option>')->implode('') !!}`;
    let rowIndex = 1;

    function updateRowCount() {
        const count = $('#itemsBody .item-row').length;
        $('#rowCount').text(count + ' ردیف');
        // فقط اگر بیش از یک ردیف داریم، دکمه حذف را فعال کن
        $('#itemsBody .btn-remove-row').prop('disabled', count <= 1);
    }

    $('#btnAddRow').on('click', function () {
        const row = `
        <tr class="item-row">
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm product-select" required>
                    <option value="">انتخاب کالا...</option>
                    ${productOptions}
                </select>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm" step="0.0001" min="0.0001" placeholder="0.00" required>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm" step="1" min="0" placeholder="اختیاری">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        </tr>`;
        $('#itemsBody').append(row);
        rowIndex++;
        updateRowCount();
    });

    $(document).on('click', '.btn-remove-row', function () {
        $(this).closest('.item-row').remove();
        updateRowCount();
    });

    $('#openingForm').on('submit', function (e) {
        const warehouse = $('#warehouseId').val();
        if (!warehouse) {
            e.preventDefault();
            alert('لطفاً ابتدا انبار را انتخاب کنید.');
            return;
        }
        const rows = $('#itemsBody .item-row').length;
        if (rows === 0) {
            e.preventDefault();
            alert('حداقل یک کالا وارد کنید.');
        }
    });
});
</script>
@endpush
