{{-- فرم مشترک درخواست خرید --}}
<div class="row g-4">
    <div class="col-lg-8">
        {{-- اطلاعات اصلی --}}
        <div class="card shadow-none border mb-4">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">اطلاعات درخواست</h6></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">تاریخ درخواست <span class="text-danger">*</span></label>
                        <input type="date" name="request_date" class="form-control @error('request_date') is-invalid @enderror"
                            value="{{ old('request_date', isset($purchaseRequest) ? $purchaseRequest->request_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                        @error('request_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">مورد نیاز تا تاریخ</label>
                        <input type="date" name="required_by_date" class="form-control"
                            value="{{ old('required_by_date', isset($purchaseRequest) ? $purchaseRequest->required_by_date?->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">اولویت <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            @foreach(\App\Models\PurchaseRequest::priorityLabels() as $k => $v)
                            <option value="{{ $k }}" @selected(old('priority', $purchaseRequest->priority ?? 'normal') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">انبار</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">انتخاب انبار...</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(old('warehouse_id', $purchaseRequest->warehouse_id ?? '') == $wh->id)>{{ $wh->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">مرکز هزینه</label>
                        <select name="cost_center_id" class="form-select">
                            <option value="">—</option>
                            @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}" @selected(old('cost_center_id', $purchaseRequest->cost_center_id ?? '') == $cc->id)>{{ $cc->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">سال مالی</label>
                        <select name="fiscal_year_id" class="form-select">
                            <option value="">—</option>
                            @foreach($fiscalYears as $fy)
                            <option value="{{ $fy->id }}" @selected(old('fiscal_year_id', $purchaseRequest->fiscal_year_id ?? '') == $fy->id)>{{ $fy->title ?? $fy->start_date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">دلیل درخواست</label>
                        <textarea name="reason" class="form-control" rows="2" placeholder="توضیح مختصری از دلیل این درخواست...">{{ old('reason', $purchaseRequest->reason ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">یادداشت</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $purchaseRequest->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ردیف‌های کالا --}}
        <div class="card shadow-none border">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">اقلام درخواست</h6>
                <button type="button" id="addRow" class="btn btn-sm btn-primary"><i class="bx bx-plus me-1"></i> افزودن ردیف</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:230px">کالا <span class="text-danger">*</span></th>
                            <th style="min-width:120px">واحد</th>
                            <th style="min-width:110px">مقدار <span class="text-danger">*</span></th>
                            <th style="min-width:130px">قیمت تخمینی</th>
                            <th style="min-width:200px">توضیح</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @php $existingItems = old('items', isset($purchaseRequest) && $purchaseRequest->exists ? $purchaseRequest->items->toArray() : []); @endphp
                        @forelse($existingItems as $idx => $item)
                        @include('warehouse.purchase-requests._item-row', ['idx' => $idx, 'item' => $item])
                        @empty
                        @include('warehouse.purchase-requests._item-row', ['idx' => 0, 'item' => []])
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('items')<div class="alert alert-danger m-3 py-2">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- ستون راست --}}
    <div class="col-lg-4">
        <div class="card shadow-none border">
            <div class="card-header border-bottom"><h6 class="card-title mb-0">خلاصه</h6></div>
            <div class="card-body">
                <p class="text-muted small mb-3">پس از ذخیره، درخواست را برای بررسی ارسال کنید.</p>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">تعداد اقلام:</span>
                    <span id="itemCount" class="fw-medium">0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">برآورد کل:</span>
                    <span id="totalEstimate" class="fw-medium">۰ ریال</span>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره پیش‌نویس</button>
                    <a href="{{ route('warehouse.purchase-requests.index') }}" class="btn btn-outline-secondary">انصراف</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- قالب ردیف (مخفی) --}}
<template id="rowTemplate">
    @include('warehouse.purchase-requests._item-row', ['idx' => '__IDX__', 'item' => []])
</template>

@push('scripts')
<script>
let rowIndex = {{ count($existingItems ?? []) }};
document.getElementById('addRow').addEventListener('click', function () {
    const tpl = document.getElementById('rowTemplate').innerHTML.replace(/__IDX__/g, rowIndex++);
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', tpl);
    updateSummary();
});
document.getElementById('itemsBody').addEventListener('click', function (e) {
    if (e.target.closest('.remove-row')) {
        e.target.closest('tr').remove();
        updateSummary();
    }
});
document.getElementById('itemsBody').addEventListener('input', updateSummary);
function updateSummary() {
    const rows = document.querySelectorAll('#itemsBody tr');
    let total = 0;
    rows.forEach(r => {
        const qty   = parseFloat(r.querySelector('input[name$="[quantity_requested]"]')?.value) || 0;
        const price = parseFloat(r.querySelector('input[name$="[estimated_unit_price]"]')?.value) || 0;
        total += qty * price;
    });
    document.getElementById('itemCount').textContent  = rows.length;
    document.getElementById('totalEstimate').textContent = total.toLocaleString('fa') + ' ریال';
}
updateSummary();
</script>
@endpush
