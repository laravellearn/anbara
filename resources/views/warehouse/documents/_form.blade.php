@php use App\Models\WarehouseDocument; @endphp

{{-- هدر سند --}}
<div class="card-body border-bottom pb-4">
    <div class="row g-3">

        {{-- نوع سند (فقط در create نمایش، در edit قابل تغییر نیست) --}}
        @if(isset($isCreate) && $isCreate)
        <input type="hidden" name="type" value="{{ $defaultType ?? 'receipt' }}">
        <div class="col-12 mb-2">
            <div class="alert alert-light border d-flex align-items-center gap-2 py-2">
                <span class="badge bg-label-{{ \App\Models\WarehouseDocument::typeColors()[$defaultType ?? 'receipt'] }} px-3 py-2 fs-6">
                    {{ \App\Models\WarehouseDocument::typeLabels()[$defaultType ?? 'receipt'] }}
                </span>
                <span class="text-muted">— تغییر نوع سند پس از ثبت ممکن نیست</span>
            </div>
        </div>
        @else
        <input type="hidden" name="type" value="{{ $document->type }}">
        @endif

        {{-- تاریخ سند --}}
        <div class="col-md-3">
            <label class="form-label">تاریخ سند <span class="text-danger">*</span></label>
            <input type="date" name="document_date" class="form-control @error('document_date') is-invalid @enderror"
                   value="{{ old('document_date', $document->document_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
            @error('document_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- شماره مرجع --}}
        <div class="col-md-3">
            <label class="form-label">شماره مرجع (فاکتور / سفارش)</label>
            <input type="text" name="reference_number" class="form-control @error('reference_number') is-invalid @enderror"
                   placeholder="اختیاری" value="{{ old('reference_number', $document->reference_number ?? '') }}">
            @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- انبار مبدأ --}}
        <div class="col-md-3">
            <label class="form-label">انبار {{ ($document->type ?? $defaultType) === 'transfer' ? 'مبدأ' : '' }} <span class="text-danger">*</span></label>
            <select name="warehouse_id" id="warehouseId" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                <option value="">انتخاب انبار...</option>
                @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" @selected(old('warehouse_id', $document->warehouse_id ?? '') == $wh->id)>{{ $wh->title }}</option>
                @endforeach
            </select>
            @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- انبار مقصد (فقط انتقال) --}}
        @php $docType = $document->type ?? $defaultType ?? ''; @endphp
        <div class="col-md-3" id="destWarehouseWrap" style="{{ $docType !== 'transfer' ? 'display:none' : '' }}">
            <label class="form-label">انبار مقصد <span class="text-danger">*</span></label>
            <select name="destination_warehouse_id" id="destWarehouseId" class="form-select @error('destination_warehouse_id') is-invalid @enderror">
                <option value="">انتخاب انبار مقصد...</option>
                @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" @selected(old('destination_warehouse_id', $document->destination_warehouse_id ?? '') == $wh->id)>{{ $wh->title }}</option>
                @endforeach
            </select>
            @error('destination_warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- طرف حساب --}}
        <div class="col-md-4">
            <label class="form-label">طرف حساب (تأمین‌کننده / مشتری)</label>
            <select name="contact_id" class="form-select @error('contact_id') is-invalid @enderror">
                <option value="">اختیاری...</option>
                @foreach($contacts as $c)
                <option value="{{ $c->id }}" @selected(old('contact_id', $document->contact_id ?? '') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('contact_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- سال مالی --}}
        <div class="col-md-4">
            <label class="form-label">سال مالی</label>
            <select name="fiscal_year_id" class="form-select @error('fiscal_year_id') is-invalid @enderror">
                <option value="">انتخاب...</option>
                @foreach($fiscalYears as $fy)
                <option value="{{ $fy->id }}" @selected(old('fiscal_year_id', $document->fiscal_year_id ?? '') == $fy->id)>{{ $fy->name }}</option>
                @endforeach
            </select>
            @error('fiscal_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- مرکز هزینه --}}
        <div class="col-md-4">
            <label class="form-label">مرکز هزینه</label>
            <select name="cost_center_id" class="form-select @error('cost_center_id') is-invalid @enderror">
                <option value="">انتخاب...</option>
                @foreach($costCenters as $cc)
                <option value="{{ $cc->id }}" @selected(old('cost_center_id', $document->cost_center_id ?? '') == $cc->id)>{{ $cc->title }}</option>
                @endforeach
            </select>
            @error('cost_center_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- توضیحات --}}
        <div class="col-12">
            <label class="form-label">توضیحات</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2">{{ old('description', $document->description ?? '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

{{-- ردیف‌های اقلام --}}
<div class="card-body">
    <h6 class="mb-3 fw-semibold"><i class="bx bx-list-ul me-1"></i> اقلام سند</h6>
    @error('items')<div class="alert alert-danger py-2 mb-3">{{ $message }}</div>@enderror

    <div class="table-responsive mb-3">
        <table class="table table-bordered align-middle" id="itemsTable" style="min-width:900px">
            <thead class="table-light">
                <tr>
                    <th style="width:30%">کالا <span class="text-danger">*</span></th>
                    <th style="width:12%">مقدار <span class="text-danger">*</span></th>
                    <th style="width:12%">قیمت واحد</th>
                    <th style="width:13%">واحد</th>
                    <th style="width:13%">شماره سریال</th>
                    <th style="width:12%">تاریخ انقضا</th>
                    <th style="width:6%" class="text-center">حذف</th>
                </tr>
            </thead>
            <tbody id="itemsBody">
                @php $existingItems = $document->items ?? collect(); @endphp
                @if($existingItems->count())
                    @foreach($existingItems as $i => $item)
                    <tr class="item-row">
                        <td>
                            <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm" required>
                                <option value="">انتخاب کالا...</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" @selected($item->product_id == $p->id)>{{ $p->title }}@if($p->sku) ({{ $p->sku }})@endif</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm" step="0.0001" value="{{ $item->quantity }}" required></td>
                        <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm" step="1" value="{{ $item->unit_price }}"></td>
                        <td>
                            <select name="items[{{ $i }}][measurement_unit_id]" class="form-select form-select-sm">
                                <option value="">پیش‌فرض</option>
                                @foreach($units as $u)<option value="{{ $u->id }}" @selected($item->measurement_unit_id == $u->id)>{{ $u->title }}</option>@endforeach
                            </select>
                        </td>
                        <td><input type="text" name="items[{{ $i }}][serial_number]" class="form-control form-control-sm" value="{{ $item->serial_number }}"></td>
                        <td><input type="date" name="items[{{ $i }}][expiry_date]" class="form-control form-control-sm" value="{{ $item->expiry_date?->format('Y-m-d') }}"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row" {{ $existingItems->count() <= 1 ? 'disabled' : '' }}>
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                @else
                <tr class="item-row">
                    <td>
                        <select name="items[0][product_id]" class="form-select form-select-sm" required>
                            <option value="">انتخاب کالا...</option>
                            @foreach($products as $p)<option value="{{ $p->id }}">{{ $p->title }}@if($p->sku) ({{ $p->sku }})@endif</option>@endforeach
                        </select>
                    </td>
                    <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm" step="0.0001" min="0.0001" required></td>
                    <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm" step="1" min="0"></td>
                    <td>
                        <select name="items[0][measurement_unit_id]" class="form-select form-select-sm">
                            <option value="">پیش‌فرض</option>
                            @foreach($units as $u)<option value="{{ $u->id }}">{{ $u->title }}</option>@endforeach
                        </select>
                    </td>
                    <td><input type="text" name="items[0][serial_number]" class="form-control form-control-sm"></td>
                    <td><input type="date" name="items[0][expiry_date]" class="form-control form-control-sm"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row" disabled><i class="bx bx-trash"></i></button>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <button type="button" id="btnAddRow" class="btn btn-outline-primary btn-sm">
        <i class="bx bx-plus me-1"></i> افزودن ردیف
    </button>
    <small class="text-muted ms-3" id="rowCount"></small>
</div>

@push('scripts')
<script>
$(function () {
    const productOptions = `@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->title }}{{ $p->sku ? ' ('.$p->sku.')' : '' }}</option>@endforeach`;
    const unitOptions    = `@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->title }}</option>@endforeach`;
    let rowIdx = $('#itemsBody .item-row').length;

    function updateRowCount() {
        const n = $('#itemsBody .item-row').length;
        $('#rowCount').text(n + ' ردیف');
        $('#itemsBody .btn-remove-row').prop('disabled', n <= 1);
    }
    updateRowCount();

    $('#btnAddRow').on('click', function () {
        const row = `
        <tr class="item-row">
            <td><select name="items[${rowIdx}][product_id]" class="form-select form-select-sm" required>
                <option value="">انتخاب کالا...</option>${productOptions}</select></td>
            <td><input type="number" name="items[${rowIdx}][quantity]" class="form-control form-control-sm" step="0.0001" min="0.0001" required></td>
            <td><input type="number" name="items[${rowIdx}][unit_price]" class="form-control form-control-sm" step="1" min="0"></td>
            <td><select name="items[${rowIdx}][measurement_unit_id]" class="form-select form-select-sm">
                <option value="">پیش‌فرض</option>${unitOptions}</select></td>
            <td><input type="text" name="items[${rowIdx}][serial_number]" class="form-control form-control-sm"></td>
            <td><input type="date" name="items[${rowIdx}][expiry_date]" class="form-control form-control-sm"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row"><i class="bx bx-trash"></i></button></td>
        </tr>`;
        $('#itemsBody').append(row);
        rowIdx++;
        updateRowCount();
    });

    $(document).on('click', '.btn-remove-row', function () {
        $(this).closest('.item-row').remove();
        updateRowCount();
    });

    // نمایش انبار مقصد فقط برای انتقال
    const docType = $('input[name="type"]').val();
    if (docType === 'transfer') $('#destWarehouseWrap').show();
});
</script>
@endpush
