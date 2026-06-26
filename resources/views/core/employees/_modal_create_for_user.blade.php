<div class="modal fade" id="createEmployeeForUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="createEmployeeForUserForm" method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">ایجاد کارمند برای کاربر</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="for_user_emp_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد کارمندی</label>
                            <input type="text" name="employee_code" class="form-control" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد ملی</label>
                            <input type="text" name="national_code" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">واحد سازمانی</label>
                            <select name="organizational_unit_id" class="form-select">
                                <option value="">انتخاب کنید</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- کاربر به‌صورت مخفی ولی ارسال می‌شود --}}
                        <input type="hidden" name="user_id" id="for_user_user_id">
                        {{-- فیلدهای دیگر: سمت، موبایل، تلفن، ایمیل، تاریخ استخدام، آدرس، توضیحات، is_active --}}
                        <div class="col-md-6">
                            <label class="form-label">سمت</label>
                            <input type="text" name="position" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">موبایل</label>
                            <input type="text" name="mobile" id="for_user_mobile" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تلفن</label>
                            <input type="text" name="phone" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاریخ استخدام</label>
                            <input type="date" name="employment_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">آدرس</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">توضیحات</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
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