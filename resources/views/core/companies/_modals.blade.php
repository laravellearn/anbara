{{-- مودال ایجاد --}}
<div class="modal fade" id="createCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">ایجاد سازمان جدید</h5>
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

            <form action="{{ route('companies.store') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام سازمان <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">شناسه ملی</label>
                            <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد اقتصادی</label>
                            <input type="text" name="economic_code" class="form-control" value="{{ old('economic_code') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">لوگو</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>

                        <div class="col-12">
                            <label class="form-label">سازمان مادر</label>
                            <select name="parent_id" class="form-select">
                                <option value="">بدون والد (سازمان اصلی)</option>
                                @foreach($parentCompanies as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="create_is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_is_active">فعال</label>
                            </div>
                        </div>
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

            <form id="editCompanyForm" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="company_id" id="edit_company_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام سازمان <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">شناسه ملی</label>
                            <input type="text" name="national_id" id="edit_national_id" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">کد اقتصادی</label>
                            <input type="text" name="economic_code" id="edit_economic_code" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">لوگو</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            @if(isset($company) && $company->logo)
                                <img src="{{ asset('storage/'.$company->logo) }}" class="img-thumbnail mt-2" width="100">
                            @endif
                        </div>

                        <div class="col-12">
                            <label class="form-label">سازمان مادر</label>
                            <select name="parent_id" id="edit_parent_id" class="form-select">
                                <option value="">بدون والد</option>
                                @foreach($parentCompanies as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">فعال</label>
                            </div>
                        </div>
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