@extends('layouts.master')

@section('title', 'پروفایل ' . $user->name)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ==================== هدر پروفایل ==================== --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                            {{-- آواتار --}}
                            <div class="avatar avatar-xl">
                                <img src="{{ $user->avatar ?? asset('images/avatar-default.png') }}" alt="{{ $user->name }}"
                                    class="rounded-circle border border-3 border-primary"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                            </div>

                            {{-- اطلاعات اصلی --}}
                            <div class="flex-grow-1 text-center text-md-start">
                                <h4 class="mb-1">{{ $user->name }}</h4>
                                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                                    @if ($user->is_active)
                                        <span class="badge bg-success">فعال</span>
                                        @if ($user->last_login_at && $user->last_login_at->gt(now()->subMinutes(5)))
                                            <span class="badge bg-info"><i class="bx bx-wifi"></i> آنلاین</span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">غیرفعال</span>
                                    @endif
                                    <span class="text-muted small">
                                        <i class="bx bx-calendar me-1"></i>
                                        عضو از {{ \Verta::instance($user->created_at)->format('Y/m/d-H:i:s') }}

                                    </span>
                                </div>
                            </div>

                            {{-- دکمه‌های عملیات --}}
                            <div class="d-flex gap-2">
                                @can('access', 'users.edit')
                                    <button class="btn btn-warning edit-user-btn" data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}" data-mobile="{{ $user->mobile }}"
                                        data-email="{{ $user->email }}"
                                        data-companies="{{ json_encode($user->companies->pluck('id')) }}"
                                        data-default-company="{{ $user->companies()->wherePivot('is_default', true)->first()?->id }}"
                                        data-roles="{{ json_encode($defaultCompanyUser ? $defaultCompanyUser->roles->pluck('id') : []) }}">
                                        <i class="bx bx-edit me-1"></i>ویرایش
                                    </button>
                                @endcan
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-arrow-back me-1"></i>بازگشت
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== اطلاعات کاربری ==================== --}}
        <div class="row g-4">
            {{-- ستون سمت راست --}}
            <div class="col-md-8">
                {{-- اطلاعات تماس --}}
                <div class="card shadow-none border mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-id-card text-primary me-2"></i>
                        <h5 class="mb-0">اطلاعات تماس</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-label-primary rounded">
                                    <i class="bx bx-mobile-alt bx-lg me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">شماره موبایل</small>
                                        <a href="tel:{{ $user->mobile }}" class="text-body fw-medium text-decoration-none"
                                            dir="ltr">
                                            {{ $user->mobile }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-label-info rounded">
                                    <i class="bx bx-envelope bx-lg me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">ایمیل</small>
                                        @if ($user->email)
                                            <a href="mailto:{{ $user->email }}"
                                                class="text-body fw-medium text-decoration-none" dir="ltr">
                                                {{ $user->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">تعریف نشده</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-label-warning rounded">
                                    <i class="bx bx-log-in-circle bx-lg me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">آخرین ورود</small>
                                        @if ($user->last_login_at)
                                            <span
                                                class="fw-medium">{{ $user->last_login_at->toJalali('Y/m/d H:i') }}</span>
                                            <br>
                                            <small class="text-muted"
                                                dir="ltr">{{ $user->last_login_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">تاکنون وارد نشده</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-label-success rounded">
                                    <i class="bx bx-calendar-check bx-lg me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">تاریخ ثبت‌نام</small>
                                        <span class="fw-medium">{{ $user->created_at->toJalali('Y/m/d H:i') }}</span>
                                        <br>
                                        <small class="text-muted"
                                            dir="ltr">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- شرکت‌ها و نقش‌ها (نسخه پیشرفته) --}}
                <div class="card shadow-none border mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="bx bx-buildings text-primary me-2"></i>
                        <h5 class="mb-0">شرکت‌ها و دسترسی‌ها</h5>
                    </div>
                    <div class="card-body">
                        @forelse($user->companies as $company)
                            @php
                                $companyUser = $user->companyUsers()->where('company_id', $company->id)->first();
                                $roles = $companyUser ? $companyUser->roles : collect();
                            @endphp
                            <div class="mb-3 p-3 bg-light rounded {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <i class="bx bx-buildings text-primary me-1"></i>
                                        <strong class="fs-6">{{ $company->name }}</strong>
                                        @if ($company->pivot->is_default)
                                            <span class="badge bg-warning ms-2">
                                                <i class="bx bx-star me-1"></i>پیش‌فرض
                                            </span>
                                        @endif
                                    </div>
                                    <span class="badge bg-secondary">{{ $roles->count() }} نقش</span>
                                </div>

                                <div class="mt-2">
                                    @if ($roles->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($roles as $role)
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-label-info p-2">
                                                        <i class="bx bx-check-circle me-1"></i>{{ $role->title }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning py-2 mb-0">
                                            <i class="bx bx-info-circle me-1"></i>
                                            <small>این کاربر در این شرکت هیچ نقشی ندارد.</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="bx bx-buildings bx-lg d-block mb-2"></i>
                                <span>به هیچ شرکتی متصل نیست.</span>
                            </div>
                        @endforelse
                    </div>
                </div>
                {{-- فعالیت‌های اخیر --}}
                <div class="card shadow-none border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div>
                            <i class="bx bx-history text-primary me-2"></i>
                            <h5 class="mb-0 d-inline">فعالیت‌های اخیر</h5>
                        </div>
                        <a href="{{ route('activity-logs.index', ['user_id' => $user->id]) }}"
                            class="btn btn-sm btn-outline-primary">
                            مشاهده همه
                        </a>
                    </div>
                    <div class="card-body">
                        @php
                            $activities = \App\Models\ActivityLog::where('user_id', $user->id)
                                ->latest()
                                ->take(10)
                                ->get();
                        @endphp

                        @forelse($activities as $activity)
                            <div class="d-flex align-items-center p-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div
                                    class="avatar avatar-sm me-3 bg-label-{{ $activity->action == 'login' ? 'success' : ($activity->action == 'delete' ? 'danger' : 'primary') }}">
                                    <i
                                        class="bx bx-{{ $activity->action == 'login' ? 'log-in-circle' : ($activity->action == 'delete' ? 'trash' : 'edit') }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small>{{ $activity->description }}</small>
                                </div>
                                <small class="text-muted"
                                    dir="ltr">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="bx bx-history bx-lg d-block mb-2"></i>
                                هیچ فعالیتی ثبت نشده است.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ستون سمت چپ --}}
            <div class="col-md-4">
                {{-- اطلاعات حساب --}}
                <div class="card shadow-none border mb-4">
                    <div class="card-header">
                        <i class="bx bx-shield-quarter text-primary me-2"></i>
                        <h5 class="mb-0 d-inline">اطلاعات حساب</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between mb-3">
                                <span class="text-muted">شناسه کاربری</span>
                                <span class="fw-medium" dir="ltr">#{{ $user->id }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-3">
                                <span class="text-muted">وضعیت</span>
                                @if ($user->is_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-danger">غیرفعال</span>
                                @endif
                            </li>
                            <li class="d-flex justify-content-between mb-3">
                                <span class="text-muted">نوع کاربر</span>
                                @if (is_null($user->tenant_id))
                                    <span class="badge bg-danger">سوپر ادمین</span>
                                @else
                                    <span class="badge bg-primary">کاربر سازمانی</span>
                                @endif
                            </li>
                            <li class="d-flex justify-content-between mb-3">
                                <span class="text-muted">تعداد شرکت‌ها</span>
                                <span class="fw-medium">{{ $user->companies->count() }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-3">
                                <span class="text-muted">تعداد نقش‌ها</span>
                                <span
                                    class="fw-medium">{{ $user->companyUsers->flatMap->roles->unique('id')->count() }}</span>
                            </li>
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">تاریخ بروزرسانی</span>
                                <span class="fw-medium small">{{ $user->updated_at->toJalali('Y/m/d H:i') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- آمار سریع --}}
                <div class="card shadow-none border">
                    <div class="card-header">
                        <i class="bx bx-bar-chart-square text-primary me-2"></i>
                        <h5 class="mb-0 d-inline">آمار سریع</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-success">
                                    <i class="bx bx-log-in-circle"></i>
                                </div>
                                <span>ورودها</span>
                            </div>
                            <span
                                class="fw-bold">{{ \App\Models\ActivityLog::where('user_id', $user->id)->where('action', 'login')->count() }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-info">
                                    <i class="bx bx-edit"></i>
                                </div>
                                <span>تغییرات</span>
                            </div>
                            <span
                                class="fw-bold">{{ \App\Models\ActivityLog::where('user_id', $user->id)->where('action', 'update')->count() }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2 bg-label-warning">
                                    <i class="bx bx-time-five"></i>
                                </div>
                                <span>آخرین فعالیت</span>
                            </div>
                            <span class="fw-bold small">
                                {{ optional(\App\Models\ActivityLog::where('user_id', $user->id)->latest()->first())->created_at?->diffForHumans() ?? '---' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== مودال ویرایش (کپی از users.index) ==================== --}}
    @include('core.users._modals')

@endsection

@push('scripts')
    <script>
        $(function() {
            // ========== متغیرهای مرتب‌سازی ==========
            let currentSort = 'created_at';
            let currentDirection = 'desc';

            // ========== فعال‌سازی Tooltip ==========
            if (typeof $.fn.tooltip !== 'undefined') {
                $('[data-bs-toggle="tooltip"]').tooltip();
            }

            // ========== تابع جستجوی AJAX ==========
            function performSearch() {
                const search = $('#liveSearch').val();
                const company = $('#filterCompany').val();
                const role = $('#filterRole').val();
                const status = $('#filterStatus').val();
                const perPage = $('#perPage').val();

                $('#usersTableWrapper').addClass('opacity-50');
                $('#searchStatus').html('<i class="bx bx-loader bx-spin"></i> در حال جستجو...');

                $.ajax({
                    url: '{{ route('users.index') }}',
                    data: {
                        search: search,
                        company_id: company,
                        role_id: role,
                        status: status,
                        per_page: perPage,
                        sort: currentSort,
                        direction: currentDirection,
                        ajax: 1
                    },
                    success: function(response) {
                        $('#usersTableWrapper').html(response.html);
                        $('#usersTableWrapper').removeClass('opacity-50');
                        $('#filteredCount').text(`(${response.total} کاربر)`);
                        $('#searchStatus').html(`نمایش ${response.total} کاربر`);

                        // فعال‌سازی مجدد tooltip ها
                        if (typeof $.fn.tooltip !== 'undefined') {
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    },
                    error: function() {
                        $('#usersTableWrapper').removeClass('opacity-50');
                        $('#searchStatus').html('<span class="text-danger">خطا در جستجو!</span>');
                    }
                });
            }

            // ========== جستجوی زنده ==========
            let searchTimeout;
            $('#liveSearch').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 500);
            });

            // ========== فیلترها ==========
            $('#filterCompany, #filterRole, #filterStatus, #perPage').on('change', function() {
                performSearch();
            });

            // ========== پاک کردن جستجو ==========
            $('#clearSearch').on('click', function() {
                $('#liveSearch').val('').focus();
                performSearch();
            });

            // ========== ریست فیلترها ==========
            $('#resetFilters').on('click', function() {
                $('#liveSearch').val('');
                $('#filterCompany').val('').trigger('change');
                $('#filterRole').val('').trigger('change');
                $('#filterStatus').val('');
                $('#perPage').val('20');
                currentSort = 'created_at';
                currentDirection = 'desc';
                performSearch();
            });

            // ========== مرتب‌سازی جدول ==========
            $(document).on('click', '.sortable', function() {
                const column = $(this).data('sort');

                if (currentSort === column) {
                    currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort = column;
                    currentDirection = 'asc';
                }

                updateSortIcons();
                performSearch();
            });

            function updateSortIcons() {
                $('.sort-icon').removeClass('active bx-sort-up bx-sort-down text-primary').addClass(
                    'bx-sort text-muted');

                const $activeHeader = $(`.sortable[data-sort="${currentSort}"]`);
                const $icon = $activeHeader.find('.sort-icon');
                $icon.removeClass('bx-sort text-muted').addClass('active text-primary');

                if (currentDirection === 'asc') {
                    $icon.addClass('bx-sort-up');
                } else {
                    $icon.addClass('bx-sort-down');
                }
            }

            // ========== تابع کمکی ایمن برای Select2 ==========
            function safeSelect2($element, options = {}) {
                if (typeof $.fn.select2 !== 'undefined') {
                    try {
                        $element.select2(options);
                    } catch (e) {
                        console.warn('Select2 error:', e.message);
                    }
                }
            }

            function safeSelect2Destroy($element) {
                if (typeof $.fn.select2 !== 'undefined' && $element.hasClass('select2-hidden-accessible')) {
                    try {
                        $element.select2('destroy');
                    } catch (e) {
                        console.warn('Select2 destroy error:', e.message);
                    }
                }
            }

            // ========== تابع بروزرسانی select پیش‌فرض ==========
            function updateDefaultSelect(checkboxesSelector, selectElement) {
                const $select = $(selectElement);

                // تخریب Select2 اگر وجود دارد
                safeSelect2Destroy($select);

                // حذف گزینه‌های قبلی (به جز اولی)
                $select.find('option:gt(0)').remove();

                // اضافه کردن گزینه‌های جدید
                $(checkboxesSelector + ':checked').each(function() {
                    const companyId = $(this).val();
                    const companyName = $(this).next('label').text().trim();
                    if (!$select.find(`option[value="${companyId}"]`).length) {
                        $select.append(`<option value="${companyId}">${companyName}</option>`);
                    }
                });

                // اگر هیچ گزینه‌ای نیست
                if ($select.find('option').length <= 1) {
                    $select.val('');
                }

                // راه‌اندازی مجدد Select2 اگر کلاس select2 را دارد
                if ($select.hasClass('select2')) {
                    safeSelect2($select, {
                        width: '100%',
                        dir: 'rtl',
                        placeholder: 'انتخاب کنید'
                    });
                }
            }

            // ========== مودال ویرایش ==========
            // ========== مودال ویرایش ==========
            $(document).on('click', '.edit-user-btn', function() {
                const btn = $(this);
                const userId = btn.data('id');
                const url = `{{ route('users.update', ':id') }}`.replace(':id', userId);

                $('#editUserForm').attr('action', url);

                // پر کردن فیلدهای متنی
                $('#edit_name').val(btn.data('name') || '');
                $('#edit_mobile').val(btn.data('mobile') || '');
                $('#edit_email').val(btn.data('email') || '');

                // پاک کردن صریح فیلدهای رمز عبور
                $('#edit_password').val('');
                $('#edit_password_confirmation').val('');

                // وضعیت
                const isActive = btn.data('is-active');
                $('#edit_is_active').prop('checked', isActive == 1 || isActive === true || isActive ===
                    '1');

                // ========== ریست همه چک‌باکس‌ها و مخفی کردن همه wrapperهای نقش ==========
                $('.edit-company-checkbox').prop('checked', false);
                $('.company-roles-wrapper').hide();

                // ========== دریافت شرکت‌های کاربر ==========
                const userCompanies = btn.data('companies') || [];

                // ========== دریافت نقش‌های کاربر برای هر شرکت ==========
                const userCompanyRoles = btn.data('company-roles') || {};

                // ========== چک کردن شرکت‌ها و نمایش نقش‌های مربوطه ==========
                userCompanies.forEach(companyId => {
                    // چک کردن شرکت
                    $(`#edit_company_${companyId}`).prop('checked', true);

                    // نمایش wrapper نقش‌ها
                    $(`#edit_company_roles_${companyId}`).show();

                    // تنظیم نقش‌های قبلی اگر وجود داشته باشد
                    const roleIds = userCompanyRoles[companyId] || [];
                    if (roleIds.length > 0) {
                        const $select = $(`#edit_company_roles_${companyId} select`);
                        $select.val(roleIds).trigger('change');
                    }
                });

                // ========== بروزرسانی سلکت شرکت پیش‌فرض ==========
                updateDefaultSelect('.edit-company-checkbox', '#edit_default_company');

                // ========== تنظیم شرکت پیش‌فرض ==========
                const defaultCompanyId = btn.data('default-company');
                if (defaultCompanyId) {
                    $('#edit_default_company').val(defaultCompanyId).trigger('change');
                }

                // نمایش مودال
                $('#editUserModal').modal('show');
            });
            // ========== شرکت‌های مودال ایجاد ==========
            $(document).on('change', '.company-checkbox', function() {
                updateDefaultSelect('.company-checkbox', '#create_default_company');
            });

            // ========== شرکت‌های مودال ویرایش ==========
            $(document).on('change', '.edit-company-checkbox', function() {
                updateDefaultSelect('.edit-company-checkbox', '#edit_default_company');
            });

            // ========== حذف کاربر ==========
            $(document).on('click', '.delete-user-btn', function() {
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
                        const form = $('<form>', {
                            method: 'POST',
                            action: url
                        }).append(
                            $('<input>', {
                                type: 'hidden',
                                name: '_token',
                                value: '{{ csrf_token() }}'
                            }),
                            $('<input>', {
                                type: 'hidden',
                                name: '_method',
                                value: 'DELETE'
                            })
                        );
                        $('body').append(form);
                        form.submit();
                    }
                });
            });

            // ========== پاک کردن فرم ایجاد ==========
            $('#createUserModal').on('hidden.bs.modal', function() {
                $('#createUserForm')[0].reset();
                $('.company-checkbox').prop('checked', false);
                $('#create_default_company').empty().append('<option value="">انتخاب کنید</option>');
                safeSelect2Destroy($('#create_default_company'));
                if ($('#create_default_company').hasClass('select2')) {
                    safeSelect2($('#create_default_company'), {
                        width: '100%',
                        dir: 'rtl',
                        placeholder: 'انتخاب کنید'
                    });
                }
                $('input[name="roles[]"]').prop('checked', false);
            });

            // ========== پاک کردن فرم ویرایش ==========
            $('#editUserModal').on('hidden.bs.modal', function() {
                $('#editUserForm')[0].reset();
                $('.edit-company-checkbox').prop('checked', false);
                $('input[name="roles[]"]', '#edit_roles_wrapper').prop('checked', false);
            });

            // ========== نمایش/مخفی کردن رمز عبور ==========
            $(document).on('click', '.toggle-password', function() {
                const input = $(this).siblings('input');
                const icon = $(this).find('i');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bx-show').addClass('bx-hide');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bx-hide').addClass('bx-show');
                }
            });

            // ========== نمایش پیام‌ها ==========
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'خطا',
                    text: '{!! addslashes($errors->first()) !!}',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
            @endif

            @if (session('swal_success'))
                Swal.fire({
                    icon: 'success',
                    title: 'موفق',
                    text: '{!! addslashes(session('swal_success')) !!}',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });

        // ========== نمایش/مخفی کردن نقش‌های هر شرکت در مودال ایجاد ==========
        $(document).on('change', '.company-checkbox', function() {
            const companyId = $(this).val();
            const $rolesWrapper = $(`#create_company_roles_${companyId}`);

            if ($(this).is(':checked')) {
                $rolesWrapper.slideDown(200);
            } else {
                $rolesWrapper.slideUp(200);
                // پاک کردن نقش‌های انتخاب شده
                $rolesWrapper.find('select').val(null).trigger('change');
            }

            // بروزرسانی شرکت پیش‌فرض
            updateDefaultSelect('.company-checkbox', '#create_default_company');
        });

        // ========== نمایش/مخفی کردن نقش‌های هر شرکت در مودال ویرایش ==========
        $(document).on('change', '.edit-company-checkbox', function() {
            const companyId = $(this).val();
            const $rolesWrapper = $(`#edit_company_roles_${companyId}`);

            if ($(this).is(':checked')) {
                $rolesWrapper.slideDown(200);
            } else {
                $rolesWrapper.slideUp(200);
                // پاک کردن نقش‌های انتخاب شده
                $rolesWrapper.find('select').val(null).trigger('change');
            }

            // بروزرسانی شرکت پیش‌فرض
            updateDefaultSelect('.edit-company-checkbox', '#edit_default_company');
        });

        // ========== تابع بروزرسانی select شرکت پیش‌فرض ==========
        // ========== تابع بروزرسانی select پیش‌فرض ==========
        function updateDefaultSelect(checkboxesSelector, selectId) {
            const $select = $(selectId);
            const $checkedCompanies = $(checkboxesSelector + ':checked');

            // ذخیره مقدار قبلی
            const oldValue = $select.val();

            // clear options
            $select.empty().append('<option value="">ابتدا شرکت انتخاب کنید</option>');

            // اضافه کردن شرکت‌های انتخاب شده
            $checkedCompanies.each(function() {
                const companyId = $(this).val();
                const companyLabel = $(this).closest('.card').find('label').text().trim();
                $select.append(`<option value="${companyId}">${companyLabel}</option>`);
            });

            // اگر شرکت قبلی هنوز انتخاب شده است، آن را نگه دار
            if (oldValue && $select.find(`option[value="${oldValue}"]`).length) {
                $select.val(oldValue);
            }

            // اگر فقط یک شرکت انتخاب شده، آن را auto-select کن
            if ($checkedCompanies.length === 1 && !oldValue) {
                $select.val($checkedCompanies.first().val());
            }
        }
    </script>
@endpush
