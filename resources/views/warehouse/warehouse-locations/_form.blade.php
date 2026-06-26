{{-- resources/views/warehouse/warehouse-locations/_form.blade.php --}}
<div class="card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">انبار <span class="text-danger">*</span></label>
            <select name="warehouse_id" class="form-select" required>
                <option value="">انتخاب کنید</option>
                @foreach($warehouses as $wh)
                <option value="{{ $wh->id }}" {{ old('warehouse_id', $location->warehouse_id ?? '') == $wh->id ? 'selected' : '' }}>{{ $wh->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">والد</label>
            <select name="parent_id" class="form-select">
                <option value="">بدون والد</option>
                @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ old('parent_id', $location->parent_id ?? '') == $loc->id ? 'selected' : '' }}>{{ $loc->code }} - {{ $loc->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">کد <span class="text-danger">*</span></label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $location->code ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">عنوان <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $location->title ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">نوع</label>
            <select name="type" class="form-select">
                <option value="">انتخاب کنید</option>
                @foreach(\App\Enums\WarehouseLocationType::options() as $value => $label)
                <option value="{{ $value }}" {{ old('type', $location->type->value ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">ترتیب</label>
            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $location->sort_order ?? 0) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">ظرفیت</label>
            <input type="number" step="any" name="capacity" class="form-control" value="{{ old('capacity', $location->capacity ?? '') }}">
        </div>
        <div class="col-md-6">
            <div class="form-check form-switch mt-4">
                <input type="hidden" name="is_active" value="0"> {{-- کلید حل مشکل --}}
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="loc_active" {{ old('is_active', $location->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="loc_active">فعال</label>
            </div>
        </div>
    </div>
</div>