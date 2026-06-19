<div class="card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">عنوان <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $product->title ?? '') }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">کد SKU</label>
            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">بارکد</label>
            <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">دسته‌بندی</label>
            <select name="category_id" class="form-select">
                <option value="">انتخاب کنید</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">برند</label>
            <select name="brand_id" class="form-select">
                <option value="">انتخاب کنید</option>
                @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{ $brand->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">واحد پایه</label>
            <select name="measurement_unit_id" class="form-select">
                <option value="">انتخاب کنید</option>
                @foreach($measurementUnits as $unit)
                <option value="{{ $unit->id }}" {{ old('measurement_unit_id', $product->measurement_unit_id ?? '') == $unit->id ? 'selected' : '' }}>{{ $unit->title }} ({{ $unit->symbol }})</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">نوع کالا</label>
            <select name="product_type_id" id="product_type_id" class="form-select">
                <option value="">انتخاب کنید</option>
                @foreach($productTypes as $type)
                <option value="{{ $type->id }}" {{ old('product_type_id', $product->product_type_id ?? '') == $type->id ? 'selected' : '' }}>{{ $type->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">حداقل موجودی</label>
            <input type="number" step="any" name="minimum_stock" class="form-control" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">حداکثر موجودی</label>
            <input type="number" step="any" name="maximum_stock" class="form-control" value="{{ old('maximum_stock', $product->maximum_stock ?? '') }}">
        </div>

        <div class="col-12">
            <label class="form-label">توضیحات</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
        </div>

        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="prod_active" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="prod_active">فعال</label>
            </div>
        </div>

        <div class="col-12"><hr><h6>واحدهای شمارشی دیگر</h6></div>
        <div class="col-12" id="additional-units">
            @if(isset($product))
            @foreach($product->measurementUnits as $index => $unit)
            <div class="row mb-2 unit-row">
                <div class="col-5">
                    <select name="measurement_units[{{ $index }}][id]" class="form-select">
                        <option value="">انتخاب واحد</option>
                        @foreach($measurementUnits as $mu)
                        <option value="{{ $mu->id }}" {{ $unit->id == $mu->id ? 'selected' : '' }}>{{ $mu->title }} ({{ $mu->symbol }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <input type="number" step="any" name="measurement_units[{{ $index }}][conversion_factor]" class="form-control" value="{{ $unit->pivot->conversion_factor ?? 1 }}" placeholder="ضریب تبدیل">
                </div>
                <div class="col-2">
                    <div class="form-check mt-2">
                        <input type="checkbox" name="measurement_units[{{ $index }}][is_default]" value="1" class="form-check-input" {{ $unit->pivot->is_default ? 'checked' : '' }}>
                        <label class="form-check-label">پیش‌فرض</label>
                    </div>
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-sm btn-danger remove-unit"><i class="bx bx-x"></i></button>
                </div>
            </div>
            @endforeach
            @endif
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-sm btn-outline-primary" id="add-unit"><i class="bx bx-plus"></i> افزودن واحد</button>
        </div>

        <div class="col-12"><hr><h6>ویژگی‌های دینامیک</h6></div>
        <div class="col-12" id="dynamic-attributes"></div>
    </div>
</div>