{{-- مودال ایجاد --}}
<div class="modal fade" id="createCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">ایجاد سازمان جدید</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('companies.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام سازمان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سازمان مادر</label>
                        <select name="parent_id" class="form-select">
                            <option value="">بدون والد (سازمان اصلی)</option>
                            @foreach($parentCompanies as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="isActive" value="1" id="create_is_active" checked>
                        <label class="form-check-label" for="create_is_active">فعال</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- مودال ویرایش --}}
<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title text-white">ویرایش سازمان</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCompanyForm" method="POST" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" name="company_id" id="edit_company_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام سازمان</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">سازمان مادر</label>
                        <select name="parent_id" id="edit_parent_id" class="form-select">
                            <option value="">بدون والد</option>
                            @foreach($parentCompanies as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="isActive" value="1" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">فعال</label>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-warning">بروزرسانی</button>
                </div>
            </form>
        </div>
    </div>
</div>