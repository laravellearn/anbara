<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="createEmployeeForm" method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">کارمند جدید</h5>
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
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="create_emp_name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد کارمندی</label>
                            <input type="text" name="employee_code" id="create_emp_code" class="form-control" value="{{ old('employee_code') }}" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد ملی</label>
                            <input type="text" name="national_code" id="create_emp_national_code" class="form-control" value="{{ old('national_code') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">واحد سازمانی</label>
                            <select name="organizational_unit_id" id="create_emp_unit" class="form-select">
                                <option value="">انتخاب کنید</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('organizational_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کاربر سیستم</label>
                            <select name="user_id" id="create_emp_user" class="form-select">
                                <option value="">بدون کاربر</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">سمت</label>
                            <input type="text" name="position" id="create_emp_position" class="form-control" value="{{ old('position') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">موبایل</label>
                            <input type="text" name="mobile" id="create_emp_mobile" class="form-control" value="{{ old('mobile') }}" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تلفن</label>
                            <input type="text" name="phone" id="create_emp_phone" class="form-control" value="{{ old('phone') }}" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" id="create_emp_email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاریخ استخدام</label>
                            <input type="date" name="employment_date" id="create_emp_employment_date" class="form-control" value="{{ old('employment_date') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">آدرس</label>
                            <textarea name="address" id="create_emp_address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">توضیحات</label>
                            <textarea name="description" id="create_emp_desc" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>

                        {{-- چک‌باکس ایجاد کاربر جدید --}}
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="create_user" value="1" id="create_user_check">
                                <label class="form-check-label" for="create_user_check">ایجاد کاربر سیستم برای این کارمند</label>
                            </div>
                        </div>

                        {{-- فیلدهای کاربر جدید (مخفی) --}}
                        <div id="createUserFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">نام کاربری <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="create_username" class="form-control" value="{{ old('username') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="create_password" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">نقش <span class="text-danger">*</span></label>
                                    <select name="role_id" id="create_role" class="form-select">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="create_emp_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_emp_active">فعال</label>
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



<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editEmployeeForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title text-white">ویرایش کارمند</h5>
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
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_emp_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد کارمندی</label>
                            <input type="text" name="employee_code" id="edit_emp_code" class="form-control" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد ملی</label>
                            <input type="text" name="national_code" id="edit_emp_national_code" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">واحد سازمانی</label>
                            <select name="organizational_unit_id" id="edit_emp_unit" class="form-select">
                                <option value="">انتخاب کنید</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کاربر سیستم</label>
                            <select name="user_id" id="edit_emp_user" class="form-select" disabled>
                                <option value="">بدون کاربر</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">سمت</label>
                            <input type="text" name="position" id="edit_emp_position" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">موبایل</label>
                            <input type="text" name="mobile" id="edit_emp_mobile" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تلفن</label>
                            <input type="text" name="phone" id="edit_emp_phone" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" id="edit_emp_email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاریخ استخدام</label>
                            <input type="date" name="employment_date" id="edit_emp_employment_date" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">آدرس</label>
                            <textarea name="address" id="edit_emp_address" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">توضیحات</label>
                            <textarea name="description" id="edit_emp_desc" class="form-control" rows="2"></textarea>
                        </div>

                        {{-- چک‌باکس ایجاد کاربر جدید (فقط در صورت نداشتن کاربر) --}}
                        <div class="col-12" id="editCreateUserCheckWrapper">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="create_user" value="1" id="edit_create_user_check">
                                <label class="form-check-label" for="edit_create_user_check">ایجاد کاربر سیستم برای این کارمند</label>
                            </div>
                        </div>

                        {{-- فیلدهای کاربر جدید (مخفی) --}}
                        <div id="editUserFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">نام کاربری <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="edit_username" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="edit_password" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">نقش <span class="text-danger">*</span></label>
                                    <select name="role_id" id="edit_role" class="form-select">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_emp_active">
                                <label class="form-check-label" for="edit_emp_active">فعال</label>
                            </div>
                        </div>
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