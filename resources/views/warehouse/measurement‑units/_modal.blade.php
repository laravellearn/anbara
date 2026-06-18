<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="unitForm" method="POST" action="{{ route('warehouse.measurement-units.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">واحد اندازه‌گیری</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="unit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نماد</label>
                        <input type="text" name="symbol" id="unit_symbol" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ضریب تبدیل</label>
                        <input type="number" step="any" name="conversion_factor" id="unit_conversion" class="form-control" value="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">والد</label>
                        <select name="parent_id" id="unit_parent" class="form-select">
                            <option value="">بدون والد</option>
                            @foreach($allUnits as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="unit_active" checked>
                        <label class="form-check-label" for="unit_active">فعال</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره</button>
                </div>
            </div>
        </form>
    </div>
</div>