<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="warehouseForm" method="POST" action="{{ route('admin.warehouses.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">انبار</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="wh_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد</label>
                            <input type="text" name="code" id="wh_code" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">آدرس</label>
                            <textarea name="address" id="wh_address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مدیر</label>
                            <select name="manager_user_id" id="wh_manager" class="form-select">
                                <option value="">انتخاب کنید</option>
                                {{-- می‌توانید کاربران Tenant را اینجا لیست کنید --}}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ظرفیت</label>
                            <input type="number" name="capacity" id="wh_capacity" class="form-control" step="0.01">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="wh_active" checked>
                                <label class="form-check-label">فعال</label>
                            </div>
                        </div>
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