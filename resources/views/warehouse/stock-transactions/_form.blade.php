<div class="card-body">
    <div class="row g-3">

        {{-- نوع تراکنش --}}
        <div class="col-md-6">
            <label class="form-label">نوع تراکنش <span class="text-danger">*</span></label>
            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                <option value="">انتخاب کنید...</option>
                @foreach(\App\Enums\InventoryTransactionType::cases() as $type)
                <option value="{{ $type->value }}" @selected(old('type', $stockTransaction->type->value ?? '') === $type->value)>
                    {{ $type->label() }}
                </option>
                @endforeach
            </select>
            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- انبار --}}
        <div class="col-md-6">
            <label class="form-label">انبار <span class="text-danger">*</span></label>
            <select name="warehouse_id" id="warehouseSelect" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                <option value="">انتخاب کنید...</option>
                @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" @selected(old('warehouse_id', $stockTransaction->warehouse_id ?? '') == $wh->id)>
                    {{ $wh->title }}
                </option>
                @endforeach
            </select>
            @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- موقعیت انبار (AJAX) --}}
        <div class="col-md-6">
            <label class="form-label">موقعیت انبار</label>
            <select name="warehouse_location_id" id="locationSelect" class="form-select @error('warehouse_location_id') is-invalid @enderror">
                <option value="">ابتدا انبار را انتخاب کنید...</option>
                @foreach($locations as $loc)
                <option value="{{ $loc->id }}" @selected(old('warehouse_location_id', $stockTransaction->warehouse_location_id ?? '') == $loc->id)>
                    {{ $loc->title }} ({{ $loc->code }})
                </option>
                @endforeach
            </select>
            @error('warehouse_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- کالا --}}
        <div class="col-md-6">
            <label class="form-label">کالا <span class="text-danger">*</span></label>
            <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                <option value="">انتخاب کنید...</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" @selected(old('product_id', $stockTransaction->product_id ?? '') == $p->id)>
                    {{ $p->title }} @if($p->sku)({{ $p->sku }})@endif
                </option>
                @endforeach
            </select>
            @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- مقدار --}}
        <div class="col-md-4">
            <label class="form-label">مقدار <span class="text-danger">*</span></label>
            <input type="number" name="quantity" step="0.0001" min="0.0001"
                   class="form-control @error('quantity') is-invalid @enderror"
                   value="{{ old('quantity', $stockTransaction->quantity ?? '') }}" required>
            @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- واحد --}}
        <div class="col-md-4">
            <label class="form-label">واحد اندازه‌گیری</label>
            <select name="measurement_unit_id" class="form-select @error('measurement_unit_id') is-invalid @enderror">
                <option value="">پیش‌فرض کالا</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" @selected(old('measurement_unit_id', $stockTransaction->measurement_unit_id ?? '') == $u->id)>
                    {{ $u->title }}
                </option>
                @endforeach
            </select>
            @error('measurement_unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- قیمت واحد --}}
        <div class="col-md-4">
            <label class="form-label">قیمت واحد (ریال)</label>
            <input type="number" name="unit_price" step="1" min="0"
                   class="form-control @error('unit_price') is-invalid @enderror"
                   value="{{ old('unit_price', $stockTransaction->unit_price ?? '') }}">
            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- سال مالی --}}
        <div class="col-md-6">
            <label class="form-label">سال مالی</label>
            <select name="fiscal_year_id" class="form-select @error('fiscal_year_id') is-invalid @enderror">
                <option value="">انتخاب کنید...</option>
                @foreach($fiscalYears as $fy)
                <option value="{{ $fy->id }}" @selected(old('fiscal_year_id', $stockTransaction->fiscal_year_id ?? '') == $fy->id)>
                    {{ $fy->name }}
                </option>
                @endforeach
            </select>
            @error('fiscal_year_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- مرکز هزینه --}}
        <div class="col-md-6">
            <label class="form-label">مرکز هزینه</label>
            <select name="cost_center_id" class="form-select @error('cost_center_id') is-invalid @enderror">
                <option value="">انتخاب کنید...</option>
                @foreach($costCenters as $cc)
                <option value="{{ $cc->id }}" @selected(old('cost_center_id', $stockTransaction->cost_center_id ?? '') == $cc->id)>
                    {{ $cc->title }}
                </option>
                @endforeach
            </select>
            @error('cost_center_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- شماره سری / بچ --}}
        <div class="col-md-4">
            <label class="form-label">شماره سریال</label>
            <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror"
                   value="{{ old('serial_number', $stockTransaction->serial_number ?? '') }}">
            @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">شماره بچ / لات</label>
            <input type="text" name="batch_number" class="form-control @error('batch_number') is-invalid @enderror"
                   value="{{ old('batch_number', $stockTransaction->batch_number ?? '') }}">
            @error('batch_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">تاریخ انقضا</label>
            <input type="date" name="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror"
                   value="{{ old('expiry_date', isset($stockTransaction->expiry_date) ? $stockTransaction->expiry_date->format('Y-m-d') : '') }}">
            @error('expiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- توضیحات --}}
        <div class="col-12">
            <label class="form-label">توضیحات</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $stockTransaction->description ?? '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    $('#warehouseSelect').on('change', function () {
        const wId = $(this).val();
        const $loc = $('#locationSelect');
        $loc.html('<option value="">در حال بارگذاری...</option>').prop('disabled', true);
        if (!wId) {
            $loc.html('<option value="">ابتدا انبار را انتخاب کنید...</option>').prop('disabled', false);
            return;
        }
        $.getJSON(`/warehouse/warehouses/${wId}/locations`, function (data) {
            let html = '<option value="">بدون موقعیت</option>';
            data.forEach(l => html += `<option value="${l.id}">${l.title} (${l.code})</option>`);
            $loc.html(html).prop('disabled', false);
        }).fail(function () {
            $loc.html('<option value="">خطا در بارگذاری</option>').prop('disabled', false);
        });
    });
});
</script>
@endpush
