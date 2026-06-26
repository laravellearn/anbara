{{-- مودال ایجاد ویژگی --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="createAttrForm" method="POST" action="{{ route('warehouse.product-attributes.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">ویژگی جدید</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                @if($errors->any() && session('show_create_modal'))
                <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
                    <strong><i class="bx bx-error-circle me-1"></i> خطا!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="create_attr_name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع <span class="text-danger">*</span></label>
                        <select name="type" id="create_attr_type" class="form-select" required>
                            <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>متن</option>
                            <option value="number" {{ old('type') == 'number' ? 'selected' : '' }}>عدد</option>
                            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>انتخاب (Select)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="createOptionsContainer" style="display: {{ old('type') == 'select' ? 'block' : 'none' }};">
                        <label class="form-label">گزینه‌ها</label>
                        <div class="tag-input-wrapper border rounded p-2" id="createTagInputWrapper">
                            <div class="d-flex flex-wrap gap-1" id="createTagContainer"></div>
                            <input type="text" id="createTagInput" class="border-0 p-0 flex-grow-1" placeholder="مقدار را وارد کرده و Enter بزنید..." style="min-width: 100px; outline: none;">
                        </div>
                        <input type="hidden" name="options" id="createOptionsHidden" value="{{ old('options') }}">
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="create_attr_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="create_attr_active">فعال</label>
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


{{-- مودال ویرایش ویژگی --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editAttrForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title text-white">ویرایش ویژگی</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                @if($errors->any() && session('show_edit_modal'))
                <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
                    <strong><i class="bx bx-error-circle me-1"></i> خطا!</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_attr_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع <span class="text-danger">*</span></label>
                        <select name="type" id="edit_attr_type" class="form-select" required>
                            <option value="text">متن</option>
                            <option value="number">عدد</option>
                            <option value="select">انتخاب (Select)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="editOptionsContainer" style="display: none;">
                        <label class="form-label">گزینه‌ها</label>
                        <div class="tag-input-wrapper border rounded p-2" id="editTagInputWrapper">
                            <div class="d-flex flex-wrap gap-1" id="editTagContainer"></div>
                            <input type="text" id="editTagInput" class="border-0 p-0 flex-grow-1" placeholder="مقدار را وارد کرده و Enter بزنید..." style="min-width: 100px; outline: none;">
                        </div>
                        <input type="hidden" name="options" id="editOptionsHidden">
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_attr_active">
                        <label class="form-check-label" for="edit_attr_active">فعال</label>
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