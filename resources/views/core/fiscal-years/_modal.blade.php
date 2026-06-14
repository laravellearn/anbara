{{-- مودال ایجاد --}}
<div class="modal fade" id="createFiscalYearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">ایجاد سال مالی جدید</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('fiscal-years.store') }}" method="POST" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام سال مالی <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="create_is_active">
                        <label class="form-check-label" for="create_is_active">سال جاری باشد؟</label>
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
<div class="modal fade" id="editFiscalYearModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title text-white">ویرایش سال مالی</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFiscalYearForm" method="POST" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" name="fiscal_year_id" id="edit_fiscal_year_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاریخ شروع</label>
                        <input type="date" name="start_date" id="edit_start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاریخ پایان</label>
                        <input type="date" name="end_date" id="edit_end_date" class="form-control" required>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">سال جاری باشد؟</label>
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