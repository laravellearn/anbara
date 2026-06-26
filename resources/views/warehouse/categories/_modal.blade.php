<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="catForm" method="POST" action="{{ route('warehouse.categories.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">دسته‌بندی</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="cat_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">والد</label>
                        <select name="parent_id" id="cat_parent" class="form-select">
                            <option value="">بدون والد</option>
                            @foreach($allCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" id="cat_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="cat_active" checked>
                        <label class="form-check-label" for="cat_active">فعال</label>
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


{{-- مودال ویرایش دسته‌بندی --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editCatForm" method="POST" action="{{ route('warehouse.categories.update', ':id') }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title text-white">ویرایش دسته‌بندی</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_cat_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">والد</label>
                        <select name="parent_id" id="edit_cat_parent" class="form-select">
                            <option value="">بدون والد</option>
                            @foreach($allCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" id="edit_cat_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0"> {{-- اضافه شد --}}
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_cat_active">
                        <label class="form-check-label" for="edit_cat_active">فعال</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-warning">بروزرسانی</button>
                </div>
            </div>
        </form>
    </div>
</div>