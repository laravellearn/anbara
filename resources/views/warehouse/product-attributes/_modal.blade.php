<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="attrForm" method="POST" action="{{ route('admin.item-attributes.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">ویژگی</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="attr_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع <span class="text-danger">*</span></label>
                        <select name="type" id="attr_type" class="form-select" required>
                            <option value="text">متن</option>
                            <option value="number">عدد</option>
                            <option value="select">انتخاب (Select)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">گزینه‌ها (برای نوع انتخاب، با کاما جدا کنید)</label>
                        <input type="text" name="options" id="attr_options" class="form-control" placeholder="قرمز,آبی,سبز">
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