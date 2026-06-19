<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="employeeForm" method="POST" action="{{ route('employees.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">کارمند</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- نمایش خطاها در بالای مودال --}}
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
                            <input type="text" name="name" id="emp_name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد کارمندی</label>
                            <input type="text" name="employee_code" id="emp_code" class="form-control" value="{{ old('employee_code') }}" maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">واحد سازمانی</label>
                            <select name="unit_id" id="emp_unit" class="form-select">
                                <option value="">انتخاب کنید</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">سمت</label>
                            <input type="text" name="position" id="emp_position" class="form-control" value="{{ old('position') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">موبایل</label>
                            <input type="text" name="mobile" id="emp_mobile" class="form-control" value="{{ old('mobile') }}" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تلفن</label>
                            <input type="text" name="phone" id="emp_phone" class="form-control" value="{{ old('phone') }}" maxlength="20">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="emp_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="emp_active">فعال</label>
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