@extends('layouts.master')

@section('title', 'لیست کاربران')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- کارت‌های آماری --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium">کل کاربران</span>
                            <h4 class="mb-0 mt-2">{{ $users->total() }}</h4>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-user bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium">کاربران فعال</span>
                            <h4 class="mb-0 mt-2">{{ $users->where('is_active', true)->count() }}</h4>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="bx bx-user-check bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium">کاربران غیرفعال</span>
                            <h4 class="mb-0 mt-2">{{ $users->where('is_active', false)->count() }}</h4>
                        </div>
                        <span class="badge bg-label-danger rounded p-2">
                            <i class="bx bx-user-x bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium">ورودهای امروز</span>
                            <h4 class="mb-0 mt-2">{{ \App\Models\ActivityLog::whereDate('created_at', today())->where('action', 'login')->count() }}</h4>
                        </div>
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="bx bx-log-in-circle bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- لیست کاربران --}}
    <div class="card">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">لیست کاربران</h5>
            <div class="d-flex gap-2 flex-wrap">
                @can('access', 'users.export')
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export"></i> خروجی
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('users.export') }}"><i class="bx bx-file me-1"></i> Excel</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bx bxs-file-pdf me-1"></i> PDF</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bx bx-printer me-1"></i> چاپ</a></li>
                    </ul>
                </div>
                @endcan
                @can('access', 'users.import')
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bx bx-import"></i> ایمپورت
                </button>
                @endcan
                @can('access', 'users.create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="bx bx-plus"></i> کاربر جدید
                </button>
                @endcan
            </div>
        </div>

        {{-- فیلترها --}}
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <select name="company_id" class="form-select select2">
                        <option value="">همه شرکت‌ها</option>
                        @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="role_id" class="form-select select2">
                        <option value="">همه نقش‌ها</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">وضعیت</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غیرفعال</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100"><i class="bx bx-filter-alt"></i> فیلتر</button>
                </div>
            </form>
        </div>

        {{-- جدول کاربران --}}
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>موبایل</th>
                        <th>ایمیل</th>
                        <th>شرکت‌ها</th>
                        <th>نقش‌ها</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="rounded-circle">
                                </div>
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td dir="ltr">{{ $user->mobile }}</td>
                        <td>{{ $user->email ?? '---' }}</td>
                        <td>
                            @foreach($user->companies as $company)
                            <span class="badge bg-label-secondary me-1">
                                {{ $company->name }}
                                @if($company->pivot->is_default)
                                <i class="bx bx-star text-warning ms-1"></i>
                                @endif
                            </span>
                            @endforeach
                        </td>
                        <td>
                            @php
                            $defaultCompanyUser = $user->companyUsers()->where('is_default', true)->first();
                            $userRoles = $defaultCompanyUser ? $defaultCompanyUser->roles : collect();
                            @endphp
                            @foreach($userRoles as $role)
                            <span class="badge bg-label-info me-1">{{ $role->title }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->is_active)
                            <span class="badge bg-success">فعال</span>
                            @else
                            <span class="badge bg-danger">غیرفعال</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @can('access', 'users.edit')
                                <button class="btn btn-sm btn-outline-warning edit-user-btn"
                                    data-id="{{ $user->id }}"
                                    data-name="{{ $user->name }}"
                                    data-mobile="{{ $user->mobile }}"
                                    data-email="{{ $user->email }}"
                                    data-companies="{{ json_encode($user->companies->pluck('id')) }}"
                                    data-default-company="{{ $user->companies()->wherePivot('is_default', true)->first()?->id }}"
                                    data-roles="{{ json_encode($userRoles->pluck('id')) }}">
                                    <i class="bx bx-edit"></i>
                                </button>
                                @endcan
                                @can('access', 'users.delete')
                                <button type="button" class="btn btn-sm btn-outline-danger delete-user-btn"
                                    data-url="{{ route('users.destroy', $user) }}"
                                    data-name="{{ $user->name }}">
                                    <i class="bx bx-trash"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">هیچ کاربری یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>

{{-- ==================== مودال ایجاد کاربر ==================== --}}
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ایجاد کاربر جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control" maxlength="11" required dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" class="form-control" dir="ltr">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تکرار رمز عبور <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        {{-- انتخاب شرکت‌ها --}}
                        <div class="col-12">
                            <label class="form-label">شرکت‌های دسترسی <span class="text-danger">*</span></label>
                            <div class="row">
                                @foreach($companies as $company)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input company-checkbox" type="checkbox" name="companies[]" value="{{ $company->id }}" id="create_company_{{ $company->id }}">
                                        <label class="form-check-label" for="create_company_{{ $company->id }}">{{ $company->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <small class="text-muted">می‌توانید چندین شرکت انتخاب کنید.</small>
                        </div>

                        {{-- شرکت پیش‌فرض --}}
                        <div class="col-12">
                            <label class="form-label">شرکت پیش‌فرض <span class="text-danger">*</span></label>
                            <select name="default_company" class="form-select" required id="create_default_company">
                                <option value="">انتخاب کنید</option>
                            </select>
                        </div>

                        {{-- نقش‌ها --}}
                        <div class="col-12">
                            <label class="form-label">نقش‌ها</label>
                            <div class="row">
                                @foreach($roles as $role)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="create_role_{{ $role->id }}">
                                        <label class="form-check-label" for="create_role_{{ $role->id }}">{{ $role->title }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره کاربر</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== مودال ویرایش کاربر ==================== --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ویرایش کاربر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
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
                            <label class="form-label">رمز عبور جدید (خالی بگذارید)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تکرار رمز جدید</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        {{-- شرکت‌ها --}}
                        <div class="col-12">
                            <label class="form-label">شرکت‌های دسترسی <span class="text-danger">*</span></label>
                            <div class="row" id="edit_companies_wrapper">
                                @foreach($companies as $company)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input edit-company-checkbox" type="checkbox" name="companies[]" value="{{ $company->id }}" id="edit_company_{{ $company->id }}">
                                        <label class="form-check-label" for="edit_company_{{ $company->id }}">{{ $company->name }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">شرکت پیش‌فرض <span class="text-danger">*</span></label>
                            <select name="default_company" class="form-select" required id="edit_default_company">
                                <option value="">انتخاب کنید</option>
                            </select>
                        </div>

                        {{-- نقش‌ها --}}
                        <div class="col-12">
                            <label class="form-label">نقش‌ها</label>
                            <div class="row" id="edit_roles_wrapper">
                                @foreach($roles as $role)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="edit_role_{{ $role->id }}">
                                        <label class="form-check-label" for="edit_role_{{ $role->id }}">{{ $role->title }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
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
            <div class="modal-header">
                <h5 class="modal-title">ایمپورت کاربران از Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">فایل Excel با فرمت مشخص شده را بارگذاری کنید.</p>
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    <small class="text-muted mt-2 d-block">
                        <a href="#" class="text-primary">دانلود فایل نمونه</a>
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">شروع ایمپورت</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function() {
        // ========== تابع کمکی برای به‌روزرسانی select شرکت پیش‌فرض ==========
        function updateDefaultSelect(checkboxesSelector, selectElement) {
            const $select = $(selectElement);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.find('option:gt(0)').remove();

            $(checkboxesSelector + ':checked').each(function () {
                const companyId = $(this).val();
                const companyName = $(this).next('label').text().trim();
                if (!$select.find(`option[value="${companyId}"]`).length) {
                    $select.append(`<option value="${companyId}">${companyName}</option>`);
                }
            });

            if ($select.find('option').length <= 1) {
                $select.val('');
            }

            if ($select.hasClass('select2')) {
                $select.select2({ width: '100%', dir: 'rtl' });
            }
        }

        // ========== مودال ایجاد ==========
        $(document).on('change', '.company-checkbox', function () {
            updateDefaultSelect('.company-checkbox', '#create_default_company');
        });

        // ========== مودال ویرایش ==========
        $(document).on('change', '.edit-company-checkbox', function () {
            updateDefaultSelect('.edit-company-checkbox', '#edit_default_company');
        });

        $('.edit-user-btn').on('click', function () {
            const btn = $(this);
            const userId = btn.data('id');
            const url = `{{ route('users.update', ':id') }}`.replace(':id', userId);

            $('#editUserForm').attr('action', url);
            $('#edit_name').val(btn.data('name'));
            $('#edit_mobile').val(btn.data('mobile'));
            $('#edit_email').val(btn.data('email'));

            $('.edit-company-checkbox').prop('checked', false);
            const userCompanies = btn.data('companies') || [];
            userCompanies.forEach(id => $(`#edit_company_${id}`).prop('checked', true));

            updateDefaultSelect('.edit-company-checkbox', '#edit_default_company');
            const defaultCompanyId = btn.data('default-company');
            if (defaultCompanyId) {
                $('#edit_default_company').val(defaultCompanyId);
            }
            if ($('#edit_default_company').hasClass('select2-hidden-accessible')) {
                $('#edit_default_company').trigger('change.select2');
            }

            $('input[name="roles[]"]', '#edit_roles_wrapper').prop('checked', false);
            const userRoles = btn.data('roles') || [];
            userRoles.forEach(id => $(`#edit_role_${id}`).prop('checked', true));

            $('#editUserModal').modal('show');
        });

        // ========== حذف با SweetAlert (روش مقاوم) ==========
        $(document).on('click', '.delete-user-btn', function () {
            const btn = $(this);
            const url = btn.data('url');
            const name = btn.data('name');

            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: `کاربر "${name}" غیرفعال خواهد شد.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'بله، حذف کن',
                cancelButtonText: 'لغو',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // ایجاد یک فرم مخفی و ارسال درخواست DELETE
                    const form = $('<form>', {
                        method: 'POST',
                        action: url
                    }).append(
                        $('<input>', { type: 'hidden', name: '_token', value: '{{ csrf_token() }}' }),
                        $('<input>', { type: 'hidden', name: '_method', value: 'DELETE' })
                    );
                    $('body').append(form);
                    form.submit();
                }
            });
        });

        // ========== نمایش خطاها و موفقیت‌ها (بدون خطای syntax) ==========
        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'خطا',
                text: '{!! addslashes($errors->first()) !!}',
                customClass: { confirmButton: 'btn btn-danger' }
            });
        @endif

        @if(session('swal_success'))
            Swal.fire({
                icon: 'success',
                title: 'موفق',
                text: '{!! addslashes(session('swal_success')) !!}',
                customClass: { confirmButton: 'btn btn-success' },
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endpush