<div class="card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">کد <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $warehouse->code ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">عنوان <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $warehouse->title ?? '') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">توضیحات</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description', $warehouse->description ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <label class="form-label">آدرس</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $warehouse->address ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
            <div class="form-check form-switch mt-4">
                <input class="form-check-input" type="checkbox" name="allow_negative_stock" value="1" id="neg_stock" {{ old('allow_negative_stock', $warehouse->allow_negative_stock ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="neg_stock">اجازه موجودی منفی</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-check form-switch mt-4">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="wh_active" {{ old('is_active', $warehouse->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="wh_active">فعال</label>
            </div>
        </div>
    </div>
</div>