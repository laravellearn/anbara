{{-- فرم مشترک فاکتور خرید --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">اطلاعات فاکتور</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">تأمین‌کننده</label>
                        <select name="supplier_id" class="form-select" id="supplierSelect">
                            <option value="">انتخاب...</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplier_id', $purchaseInvoice->supplier_id ?? '') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">شماره فاکتور تأمین‌کننده</label>
                        <input type="text" name="supplier_invoice_number" class="form-control"
                            value="{{ old('supplier_invoice_number', $purchaseInvoice->supplier_invoice_number ?? '') }}" placeholder="شماره فاکتور تأمین‌کننده...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">سفارش خرید مرتبط</label>
                        <select name="purchase_order_id" class="form-select" id="poSelect">
                            <option value="">بدون سفارش مرتبط</option>
                            @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" @selected(old('purchase_order_id', $purchaseInvoice->purchase_order_id ?? '') == $po->id)>{{ $po->po_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">مرکز هزینه</label>
                        <select name="cost_center_id" class="form-select">
                            <option value="">—</option>
                            @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}" @selected(old('cost_center_id', $purchaseInvoice->cost_center_id ?? '') == $cc->id)>{{ $cc->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">تاریخ فاکتور <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
                            value="{{ old('invoice_date', isset($purchaseInvoice) ? $purchaseInvoice->invoice_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                        @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">تاریخ سررسید</label>
                        <input type="date" name="due_date" class="form-control"
                            value="{{ old('due_date', isset($purchaseInvoice) ? $purchaseInvoice->due_date?->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">سال مالی</label>
                        <select name="fiscal_year_id" class="form-select">
                            <option value="">—</option>
                            @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}" @selected(old('fiscal_year_id', $purchaseInvoice->fiscal_year_id ?? '') == $fy->id)>{{ $fy->title ?? $fy->start_date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">یادداشت</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $purchaseInvoice->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- اقلام --}}
        <div class="card shadow-none border">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">اقلام فاکتور</h6>
                <button type="button" id="addRow" class="btn btn-sm btn-primary"><i class="bx bx-plus me-1"></i> افزودن ردیف</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:220px">کالا <span class="text-danger">*</span></th>
                            <th style="min-width:120px">واحد</th>
                            <th style="min-width:110px">تعداد <span class="text-danger">*</span></th>
                            <th style="min-width:130px">قیمت واحد <span class="text-danger">*</span></th>
                            <th style="min-width:90px">تخفیف %</th>
                            <th style="min-width:110px">جمع ردیف</th>
                            <th style="min-width:180px">توضیح</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @php $existingItems = old('items', isset($purchaseInvoice) && $purchaseInvoice->exists ? $purchaseInvoice->items->toArray() : []); @endphp
                        @forelse($existingItems as $idx => $item)
                        @include('warehouse.purchase-invoices._item-row', ['idx' => $idx, 'item' => $item])
                        @empty
                        @include('warehouse.purchase-invoices._item-row', ['idx' => 0, 'item' => []])
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('items')<div class="alert alert-danger m-3 py-2">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- ستون راست --}}
    <div class="col-lg-4">
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">شرایط مالی</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-medium">تخفیف کلی (%)</label>
                    <input type="number" name="discount_percent" id="discountPercent" class="form-control" min="0" max="100" step="0.01"
                        value="{{ old('discount_percent', $purchaseInvoice->discount_percent ?? 0) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">مالیات (%)</label>
                    <input type="number" name="tax_percent" id="taxPercent" class="form-control" min="0" max="100" step="0.01"
                        value="{{ old('tax_percent', $purchaseInvoice->tax_percent ?? 9) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">هزینه حمل</label>
                    <input type="number" name="shipping_cost" id="shippingCost" class="form-control" min="0" step="0.01"
                        value="{{ old('shipping_cost', $purchaseInvoice->shipping_cost ?? 0) }}">
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">جمع اقلام:</span><span id="subtotal">—</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">تخفیف:</span><span id="discountAmt" class="text-danger">—</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">مالیات:</span><span id="taxAmt">—</span></div>
                <div class="d-flex justify-content-between mb-1"><span class="text-muted">حمل:</span><span id="shippingAmt">—</span></div>
                <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-2 mt-2">
                    <span>مبلغ کل:</span><span id="grandTotal" class="text-primary">—</span>
                </div>
            </div>
        </div>
        <div class="card shadow-none border">
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره فاکتور</button>
                    <a href="{{ route('warehouse.purchase-invoices.index') }}" class="btn btn-outline-secondary">انصراف</a>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="rowTemplate">
    @include('warehouse.purchase-invoices._item-row', ['idx' => '__IDX__', 'item' => []])
</template>

@push('scripts')
<script>
let rowIndex = {{ count($existingItems ?? []) }};
document.getElementById('addRow').addEventListener('click', function () {
    const tpl = document.getElementById('rowTemplate').innerHTML.replace(/__IDX__/g, rowIndex++);
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', tpl);
    recalc();
});
document.getElementById('itemsBody').addEventListener('click', function (e) {
    if (e.target.closest('.remove-row')) { e.target.closest('tr').remove(); recalc(); }
});
document.getElementById('itemsBody').addEventListener('input', recalc);
document.getElementById('discountPercent').addEventListener('input', recalc);
document.getElementById('taxPercent').addEventListener('input', recalc);
document.getElementById('shippingCost').addEventListener('input', recalc);

function recalc() {
    let sub = 0;
    document.querySelectorAll('#itemsBody tr').forEach(r => {
        const qty  = parseFloat(r.querySelector('[name$="[quantity]"]')?.value) || 0;
        const up   = parseFloat(r.querySelector('[name$="[unit_price]"]')?.value) || 0;
        const disc = parseFloat(r.querySelector('[name$="[discount_percent]"]')?.value) || 0;
        const line = qty * up * (1 - disc / 100);
        const cell = r.querySelector('.line-total');
        if (cell) cell.textContent = line.toLocaleString('fa');
        sub += line;
    });
    const discP  = parseFloat(document.getElementById('discountPercent').value) || 0;
    const taxP   = parseFloat(document.getElementById('taxPercent').value) || 0;
    const ship   = parseFloat(document.getElementById('shippingCost').value) || 0;
    const disc   = sub * discP / 100;
    const tax    = (sub - disc) * taxP / 100;
    const total  = sub - disc + tax + ship;
    document.getElementById('subtotal').textContent   = sub.toLocaleString('fa') + ' ریال';
    document.getElementById('discountAmt').textContent = disc.toLocaleString('fa') + ' ریال';
    document.getElementById('taxAmt').textContent      = tax.toLocaleString('fa') + ' ریال';
    document.getElementById('shippingAmt').textContent = ship.toLocaleString('fa') + ' ریال';
    document.getElementById('grandTotal').textContent  = total.toLocaleString('fa') + ' ریال';
}
recalc();
</script>
@endpush
