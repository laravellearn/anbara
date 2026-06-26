<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="unitForm" method="POST" action="{{ route('organizational-units.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">واحد سازمانی جدید</h5>
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
                        <input type="text" name="name" id="unit_name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">کد</label>
                        <input type="text" name="code" id="unit_code" class="form-control" value="{{ old('code') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">والد</label>
                        <select name="parent_id" id="unit_parent" class="form-select">
                            <option value="">بدون والد</option>
                            @foreach($allUnits as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مدیر</label>
                        <select name="manager_user_id" id="unit_manager" class="form-select">
                            <option value="">انتخاب نشده</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('manager_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" id="unit_desc" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="unit_active" {{ old('is_active', '1') ? 'checked' : '' }}>
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


<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editUnitForm" method="POST" action="{{ route('organizational-units.update', ':id') }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title text-white">ویرایش واحد سازمانی</h5>
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
                        <input type="text" name="name" id="edit_unit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">کد</label>
                        <input type="text" name="code" id="edit_unit_code" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">والد</label>
                        <select name="parent_id" id="edit_unit_parent" class="form-select">
                            <option value="">بدون والد</option>
                            @foreach($allUnits as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مدیر</label>
                        <select name="manager_user_id" id="edit_unit_manager" class="form-select">
                            <option value="">انتخاب نشده</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" id="edit_unit_desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_unit_active">
                        <label class="form-check-label" for="edit_unit_active">فعال</label>
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