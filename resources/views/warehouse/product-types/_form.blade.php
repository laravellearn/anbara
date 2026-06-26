{{-- resources/views/warehouse/product-types/_form.blade.php --}}
<div class="card-body">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">عنوان <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $productType->title ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">توضیحات</label>
            <textarea name="description" class="form-control" rows="2">{{ old('description', $productType->description ?? '') }}</textarea>
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="pt_active" {{ old('is_active', $productType->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="pt_active">فعال</label>
            </div>
        </div>
        <div class="col-12"><hr><h6>ویژگی‌های مرتبط</h6></div>
        <div class="col-md-4">
            <input type="text" id="attributeSearch" class="form-control" placeholder="جستجوی ویژگی...">
        </div>
        <div class="col-12" id="attributes-container">
            @foreach($attributes as $attr)
                @php
                    $pivot = isset($productType) ? $productType->attributes->find($attr->id) : null;
                    $checked = old("attributes.{$attr->id}.id") ? true : ($pivot ? true : false);
                @endphp
                <div class="row mb-2 align-items-end attribute-row" data-attr-name="{{ $attr->name }}">
                    <div class="col-md-1">
                        <div class="form-check">
                            <input class="form-check-input attr-checkbox" type="checkbox" name="attributes[{{ $attr->id }}][id]" value="{{ $attr->id }}" id="attr_{{ $attr->id }}" {{ $checked ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="attr_{{ $attr->id }}">{{ $attr->name }} ({{ $attr->type }})</label>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="attributes[{{ $attr->id }}][is_required]" value="1" {{ old("attributes.{$attr->id}.is_required", $pivot->is_required ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">اجباری</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="attributes[{{ $attr->id }}][sort_order]" class="form-control" placeholder="ترتیب" value="{{ old("attributes.{$attr->id}.sort_order", $pivot->sort_order ?? 0) }}">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#attributeSearch').on('keyup', function() {
            var search = $(this).val().toLowerCase();
            $('#attributes-container .attribute-row').each(function() {
                var name = $(this).data('attr-name').toLowerCase();
                $(this).toggle(name.indexOf(search) > -1);
            });
        });
    });
</script>
@endpush