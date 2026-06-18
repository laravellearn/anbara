<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="contactForm" method="POST" action="{{ route('warehouse.contacts.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">مخاطب</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نوع <span class="text-danger">*</span></label>
                            <select name="type" id="contact_type" class="form-select" required>
                                <option value="customer">مشتری</option>
                                <option value="supplier">تأمین‌کننده</option>
                                <option value="both">هر دو</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام</label>
                            <input type="text" name="first_name" id="contact_first_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام خانوادگی</label>
                            <input type="text" name="last_name" id="contact_last_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام شرکت</label>
                            <input type="text" name="company_name" id="contact_company_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">موبایل</label>
                            <input type="text" name="mobile" id="contact_mobile" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تلفن</label>
                            <input type="text" name="phone" id="contact_phone" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" id="contact_email" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="contact_is_active" checked>
                                <label class="form-check-label" for="contact_is_active">فعال</label>
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