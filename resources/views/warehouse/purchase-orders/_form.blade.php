{{-- فرم مشترک ایجاد/ویرایش سفارش خرید --}}
<div class="row g-4">
    {{-- ستون چپ: اطلاعات اصلی --}}
    <div class="col-lg-8">
        {{-- کارت اطلاعات اصلی --}}
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">اطلاعات سفارش</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">تأمین‌کننده</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">انتخاب تأمین‌کننده...</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplier_id', $purchaseOrder->supplier_id ?? '') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">انبار دریافت <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                            <option value="">انتخاب انبار...</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(old('warehouse_id', $purchaseOrder->warehouse_id ?? '') == $wh->id)>{{ $wh->title }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">تاریخ سفارش <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror"
                               value="{{ old('order_date', isset($purchaseOrder) ? $purchaseOrder->order_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                        @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">تاریخ تحویل پیش‌بینی</label>
                        <input type="date" name="expected_delivery_date" class="form-control"
                               value="{{ old('expected_delivery_date', isset($purchaseOrder) ? $purchaseOrder->expected_delivery_date?->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">شماره مرجع</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="شماره فاکتور تأمین‌کننده..."
                               value="{{ old('reference_number', $purchaseOrder->reference_number ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">مرکز هزینه</label>
                        <select name="cost_center_id" class="form-select">
                            <option value="">—</option>
                            @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}" @selected(old('cost_center_id', $purchaseOrder->cost_center_id ?? '') == $cc->id)>{{ $cc->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">سال مالی</label>
                        <select name="fiscal_year_id" class="form-select">
                            <option value="">—</option>
                            @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}" @selected(old('fiscal_year_id', $purchaseOrder->fiscal_year_id ?? '') == $fy->id)>{{ $fy->title ?? $fy->start_date }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- ردیف‌های کالا --}}
        <div class="card shadow-none border">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">اقلام سفارش</h6>
                <button type="button" id="addRow" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> افزودن ردیف
                </button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:220px">کالا <span class="text-danger">*</span></th>
                            <th style="min-width:120px">واحد</th>
                            <th style="min-width:110px">مقدار <span class="text-danger">*</span></th>
                            <th style="min-width:130px">قیمت واحد</th>
                            <th style="min-width:90px">تخفیف %</th>
                            <th style="min-width:110px">جمع ردیف</th>
                            <th style="min-width:200px">توضیح</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @php $existingItems = old('items', isset($purchaseOrder) ? $purchaseOrder->items->toArray() : []); @endphp
                        @forelse($existingItems as $idx => $item)
                        @include('warehouse.purchase-orders._item-row', ['idx' => $idx, 'item' => $item, 'products' => $products, 'units' => $units])
                        @empty
                        @include('warehouse.purchase-orders._item-row', ['idx' => 0, 'item' => [], 'products' => $products, 'units' => $units])
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('items')<div class="alert alert-danger m-3 py-2">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- ستون راست: مالی و یادداشت --}}
    <div class="col-lg-4">
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">شرایط مالی</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-medium">تخفیف کلی (%)</label>
                    <input type="number" name="discount_percent" class="form-control" min="0" max="100" step="0.01"
                           value="{{ old('discount_percent', $purchaseOrder->discount_percent ?? 0) }}" id="discountPercent">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">مالیات (%)</label>
                    <input type="number" name="tax_percent" class="form-control" min="0" max="100" step="0.01"
                           value="{{ old('tax_percent', $purchaseOrder->tax_percent ?? 9) }}" id="taxPercent">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">هزینه حمل</label>
                    <input type="number" name="shipping_cost" class="form-control" min="0" step="0.01"
                           value="{{ old('shipping_cost', $purchaseOrder->shipping_cost ?? 0) }}" id="shippingCost">
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">جمع کالا</span><strong id="summarySubtotal">۰</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">تخفیف</span><strong id="summaryDiscount" class="text-danger">۰</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">مالیات</span><strong id="summaryTax">۰</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">حمل</span><strong id="summaryShipping">۰</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold fs-6">جمع نهایی</span>
                    <strong class="fs-5 text-primary" id="summaryTotal">۰ ﷼</strong>
                </div>
            </div>
        </div>
        <div class="card shadow-none border">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">یادداشت و شرایط</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">یادداشت داخلی</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $purchaseOrder->notes ?? '') }}</textarea>
                </div>
                <div>
                    <label class="form-label">شرایط و مقررات</label>
                    <textarea name="terms_and_conditions" class="form-control" rows="3">{{ old('terms_and_conditions', $purchaseOrder->terms_and_conditions ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Template ردیف کالا برای افزودن پویا --}}
<template id="rowTemplate">
    @include('warehouse.purchase-orders._item-row', ['idx' => '__IDX__', 'item' => [], 'products' => $products, 'units' => $units])
</template>

@push('scripts')
<script>
(function () {
    let idx = {{ count($existingItems ?? []) }};

    document.getElementById('addRow').addEventListener('click', function () {
        const tpl  = document.getElementById('rowTemplate').innerHTML.replace(/__IDX__/g, idx++);
        const tbody = document.getElementById('itemsBody');
        tbody.insertAdjacentHTML('beforeend', tpl);
        recalc();
    });

    document.getElementById('itemsBody').addEventListener('click', function (e) {
        if (e.target.closest('.removeRow')) {
            e.target.closest('tr').remove();
            recalc();
        }
    });

    document.getElementById('itemsBody').addEventListener('input', recalc);
    document.getElementById('discountPercent').addEventListener('input', recalc);
    document.getElementById('taxPercent').addEventListener('input', recalc);
    document.getElementById('shippingCost').addEventListener('input', recalc);

    function recalc() {
        let subtotal = 0;
        document.querySelectorAll('#itemsBody tr').forEach(function (row) {
            const qty   = parseFloat(row.querySelector('[name$="[quantity_ordered]"]')?.value) || 0;
            const price = parseFloat(row.querySelector('[name$="[unit_price]"]')?.value) || 0;
            const disc  = parseFloat(row.querySelector('[name$="[discount_percent]"]')?.value) || 0;
            const line  = qty * price * (1 - disc / 100);
            subtotal   += line;
            const lineEl = row.querySelector('.lineTotal');
            if (lineEl) lineEl.textContent = line.toLocaleString('fa-IR', {maximumFractionDigits: 0});
        });

        const discPct  = parseFloat(document.getElementById('discountPercent').value) || 0;
        const taxPct   = parseFloat(document.getElementById('taxPercent').value) || 0;
        const shipping = parseFloat(document.getElementById('shippingCost').value) || 0;

        const discount = subtotal * discPct / 100;
        const tax      = (subtotal - discount) * taxPct / 100;
        const total    = subtotal - discount + tax + shipping;

        const fmt = v => v.toLocaleString('fa-IR', {maximumFractionDigits: 0});
        document.getElementById('summarySubtotal').textContent = fmt(subtotal);
        document.getElementById('summaryDiscount').textContent = fmt(discount);
        document.getElementById('summaryTax').textContent      = fmt(tax);
        document.getElementById('summaryShipping').textContent = fmt(shipping);
        document.getElementById('summaryTotal').textContent    = fmt(total) + ' ﷼';
    }

    recalc();
})();
</script>
@endpush
