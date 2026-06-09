{{-- ==================== مودال ایجاد کاربر ==================== --}}
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">ایجاد کاربر جدید</h5>
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
            
            <form action="{{ route('users.store') }}" method="POST" id="createUserForm" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror" 
                                   value="{{ old('mobile') }}" maxlength="11" required dir="ltr">
                            @error('mobile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" dir="ltr">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                       required minlength="8">
                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                    <i class="bx bx-show"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">تکرار رمز عبور <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                    <i class="bx bx-show"></i>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">وضعیت کاربر</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       id="create_is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_is_active">فعال</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="text-muted mb-3"><i class="bx bx-buildings me-1"></i>دسترسی به شرکت‌ها و نقش‌ها</h6>
                        </div>

                        <div class="col-12" id="create_companies_container">
                            @foreach($companies as $company)
                            <div class="card border shadow-none mb-3">
                                <div class="card-body py-2 px-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input company-checkbox" type="checkbox" 
                                               name="companies[]" value="{{ $company->id }}" 
                                               id="create_company_{{ $company->id }}"
                                               {{ in_array($company->id, old('companies', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-medium" for="create_company_{{ $company->id }}">
                                            <i class="bx bx-buildings me-1"></i>{{ $company->name }}
                                        </label>
                                    </div>
                                    <div class="company-roles-wrapper" id="create_company_roles_{{ $company->id }}" 
                                         style="{{ in_array($company->id, old('companies', [])) ? 'display:block' : 'display:none' }}">
                                        <label class="form-label small text-muted">نقش‌ها در این شرکت:</label>
                                        <select class="form-select select2-roles" 
                                                name="company_roles[{{ $company->id }}][]" 
                                                multiple="multiple" 
                                                data-company-id="{{ $company->id }}"
                                                style="width:100%">
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                {{ in_array($role->id, old('company_roles.' . $company->id, [])) ? 'selected' : '' }}>
                                                {{ $role->title }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">شرکت پیش‌فرض <span class="text-danger">*</span></label>
                            <select name="default_company" class="form-select" required id="create_default_company">
                                <option value="">ابتدا شرکت انتخاب کنید</option>
                                @foreach(old('companies', []) as $companyId)
                                    @php $company = $companies->find($companyId); @endphp
                                    @if($company)
                                        <option value="{{ $company->id }}" 
                                            {{ old('default_company') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره کاربر</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== مودال ویرایش کاربر ==================== --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title text-white">ویرایش کاربر</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            {{-- نمایش خطاهای ویرایش --}}
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
            
            <form id="editUserForm" method="POST" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" id="edit_mobile" class="form-control" maxlength="11" required dir="ltr">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" id="edit_email" class="form-control" dir="ltr">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">وضعیت کاربر</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">فعال</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">رمز عبور جدید</label>
                            <div class="input-group">
                                <input type="password" name="password" id="edit_password" class="form-control" placeholder="خالی بگذارید" minlength="8">
                                <span class="input-group-text toggle-password" style="cursor: pointer;"><i class="bx bx-show"></i></span>
                            </div>
                            <small class="text-muted">در صورت نیاز به تغییر رمز، پر کنید.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">تکرار رمز جدید</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="edit_password_confirmation" class="form-control" placeholder="تکرار رمز جدید" minlength="8">
                                <span class="input-group-text toggle-password" style="cursor: pointer;"><i class="bx bx-show"></i></span>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="text-muted mb-3"><i class="bx bx-buildings me-1"></i>دسترسی به شرکت‌ها و نقش‌ها</h6>
                        </div>

                        <div class="col-12" id="edit_companies_container">
                            @foreach($companies as $company)
                            <div class="card border shadow-none mb-3">
                                <div class="card-body py-2 px-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input edit-company-checkbox" type="checkbox" 
                                               name="companies[]" value="{{ $company->id }}" 
                                               id="edit_company_{{ $company->id }}">
                                        <label class="form-check-label fw-medium" for="edit_company_{{ $company->id }}">
                                            <i class="bx bx-buildings me-1"></i>{{ $company->name }}
                                        </label>
                                    </div>
                                    <div class="company-roles-wrapper" id="edit_company_roles_{{ $company->id }}" style="display: none;">
                                        <label class="form-label small text-muted">نقش‌ها در این شرکت:</label>
                                        <select class="form-select select2-roles" 
                                                name="company_roles[{{ $company->id }}][]" 
                                                multiple="multiple" 
                                                data-company-id="{{ $company->id }}"
                                                style="width:100%">
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">شرکت پیش‌فرض <span class="text-danger">*</span></label>
                            <select name="default_company" class="form-select" required id="edit_default_company">
                                <option value="">ابتدا شرکت انتخاب کنید</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-warning">بروزرسانی کاربر</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ==================== مودال ایمپورت ==================== --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white">
                    <i class="bx bx-import me-1"></i>ایمپورت کاربران از Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bx bx-file bx-lg text-muted"></i>
                        <p class="mt-2">فایل Excel کاربران را با فرمت مشخص شده بارگذاری کنید.</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">فایل Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                        <small class="text-muted">فرمت‌های مجاز: xlsx. و xls.</small>
                    </div>
                    <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                        <i class="bx bx-info-circle me-2"></i>
                        <div>
                            <strong>توجه:</strong> ستون‌های فایل باید دقیقاً مطابق فایل نمونه باشد.
                            <br>
                            <a href="#" class="text-primary fw-medium mt-1 d-inline-block">
                                <i class="bx bx-download me-1"></i>دانلود فایل نمونه
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-info text-white">شروع ایمپورت</button>
                </div>
            </form>
        </div>
    </div>
</div>