@extends('layouts.master')

@section('title', 'لیست کاربران')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ==================== کارت‌های آماری ==================== --}}
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-medium text-muted">کل کاربران</span>
                                <h3 class="mb-0 mt-1">{{ $users->total() }}</h3>
                                <small class="text-success"><i class="bx bx-user-plus"></i>
                                    +{{ \App\Models\User::whereDate('created_at', today())->count() }} امروز</small>
                            </div>
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="bx bx-user bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-medium text-muted">کاربران فعال</span>
                                <h3 class="mb-0 mt-1">{{ $users->where('is_active', true)->count() }}</h3>
                                <small
                                    class="text-muted">{{ number_format(($users->where('is_active', true)->count() / max(1, $users->total())) * 100, 1) }}%
                                    کل</small>
                            </div>
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-user-check bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-medium text-muted">کاربران غیرفعال</span>
                                <h3 class="mb-0 mt-1">{{ $users->where('is_active', false)->count() }}</h3>
                                <small class="text-warning">{{ $users->where('is_active', false)->count() }} کاربر</small>
                            </div>
                            <span class="badge bg-label-danger rounded p-2">
                                <i class="bx bx-user-x bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-medium text-muted">آنلاین (۵ دقیقه اخیر)</span>
                                <h3 class="mb-0 mt-1">
                                    {{ \App\Models\ActivityLog::where('action', 'login')->where('created_at', '>=', now()->subMinutes(5))->distinct('user_id')->count() }}
                                </h3>
                                <small class="text-info"><i class="bx bx-wifi"></i> همین الان</small>
                            </div>
                            <span class="badge bg-label-info rounded p-2">
                                <i class="bx bx-wifi bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== جستجوی زنده + ابزارها ==================== --}}
        <div class="card shadow-none border mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-medium"><i class="bx bx-search-alt me-1"></i>جستجوی زنده</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                            <input type="text" id="liveSearch" class="form-control"
                                placeholder="نام، موبایل یا ایمیل کاربر را جستجو کنید..." autocomplete="off">
                            <span class="input-group-text bg-white cursor-pointer" id="clearSearch"
                                style="cursor: pointer;">
                                <i class="bx bx-x"></i>
                            </span>
                        </div>
                        <small class="text-muted" id="searchStatus">در حال جستجو در {{ $users->total() }} کاربر...</small>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-medium">شرکت</label>
                        <select id="filterCompany" class="form-select select2">
                            <option value="">همه شرکت‌ها</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-medium">نقش</label>
                        <select id="filterRole" class="form-select select2">
                            <option value="">همه نقش‌ها</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label fw-medium">وضعیت</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">همه</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غیرفعال
                            </option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label fw-medium">نمایش</label>
                        <select id="perPage" class="form-select">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>۱۰</option>
                            <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>۲۰</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>۵۰</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>۱۰۰</option>
                        </select>
                    </div>

                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters"
                            data-bs-toggle="tooltip" title="حذف همه فیلترها">
                            <i class="bx bx-reset"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== لیست کاربران ==================== --}}
        <div class="card shadow-none border">
            <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
                <h5 class="card-title mb-0">
                    <i class="bx bx-group me-1"></i>لیست کاربران
                    <small class="text-muted ms-2" id="filteredCount">({{ $users->total() }} کاربر)</small>
                </h5>
                <div class="d-flex gap-2 flex-wrap">
                    @can('access', 'users.export')
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip"
                                title="خروجی گرفتن">
                                <i class="bx bx-export"></i> خروجی
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('users.export') }}"><i
                                            class="bx bx-file me-1"></i> Excel</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bx bxs-file-pdf me-1"></i> PDF</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bx bx-printer me-1"></i> چاپ</a></li>
                            </ul>
                        </div>
                    @endcan
                    @can('access', 'users.import')
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#importModal" data-bs-toggle="tooltip" title="ایمپورت از فایل Excel">
                            <i class="bx bx-import"></i> ایمپورت
                        </button>
                    @endcan
                    @can('access', 'users.create')
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createUserModal" data-bs-toggle="tooltip" title="ایجاد کاربر جدید">
                            <i class="bx bx-plus"></i> کاربر جدید
                        </button>
                    @endcan
                </div>
            </div>

            <div class="table-responsive" id="usersTableWrapper">
                @include('core.users._table', ['users' => $users])
            </div>
        </div>
    </div>

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

                        if (typeof $.fn.tooltip !== 'undefined') {
                            $('[data-bs-toggle="tooltip"]').tooltip();
                        }
                    },
                    error: function() {
                        $('#usersTableWrapper').removeClass('opacity-50');
                        $('#searchStatus').html('<span class="text-danger">خطا در جستجو!</span>');
                        if (typeof showToast !== 'undefined') {
                            showToast('خطا در ارتباط با سرور', 'error');
                        }
                    }
                });
            }

            // ========== جستجوی زنده ==========
            let searchTimeout;
            $('#liveSearch').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 500);
            });

            $('#filterCompany, #filterRole, #filterStatus, #perPage').on('change', function() {
                performSearch();
            });

            $('#clearSearch').on('click', function() {
                $('#liveSearch').val('').focus();
                performSearch();
            });

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

            // ========== توابع Select2 ==========
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
            function updateDefaultSelect(checkboxesSelector, selectId) {
                const $select = $(selectId);
                const $checkedCompanies = $(checkboxesSelector + ':checked');
                const oldValue = $select.val();

                safeSelect2Destroy($select);
                $select.empty().append('<option value="">ابتدا شرکت انتخاب کنید</option>');

                $checkedCompanies.each(function() {
                    const companyId = $(this).val();
                    const companyLabel = $(this).closest('.card').find('label').text().trim();
                    $select.append(`<option value="${companyId}">${companyLabel}</option>`);
                });

                if (oldValue && $select.find(`option[value="${oldValue}"]`).length) {
                    $select.val(oldValue);
                }
                if ($checkedCompanies.length === 1 && !oldValue) {
                    $select.val($checkedCompanies.first().val());
                }

                if ($select.hasClass('select2')) {
                    safeSelect2($select, {
                        width: '100%',
                        dir: 'rtl',
                        placeholder: 'انتخاب کنید'
                    });
                }
            }

            // ========== نمایش/مخفی کردن نقش‌های هر شرکت ==========
            $(document).on('change', '.company-checkbox', function() {
                const companyId = $(this).val();
                const $rolesWrapper = $(`#create_company_roles_${companyId}`);
                if ($(this).is(':checked')) {
                    $rolesWrapper.slideDown(200);
                } else {
                    $rolesWrapper.slideUp(200);
                    $rolesWrapper.find('select').val(null).trigger('change');
                }
                updateDefaultSelect('.company-checkbox', '#create_default_company');
            });

            $(document).on('change', '.edit-company-checkbox', function() {
                const companyId = $(this).val();
                const $rolesWrapper = $(`#edit_company_roles_${companyId}`);
                if ($(this).is(':checked')) {
                    $rolesWrapper.slideDown(200);
                } else {
                    $rolesWrapper.slideUp(200);
                    $rolesWrapper.find('select').val(null).trigger('change');
                }
                updateDefaultSelect('.edit-company-checkbox', '#edit_default_company');
            });

            // ========== مودال ویرایش ==========
            $(document).on('click', '.edit-user-btn', function() {
                const btn = $(this);
                const userId = btn.data('id');
                const url = `{{ route('users.update', ':id') }}`.replace(':id', userId);

                $('#editUserForm').attr('action', url);
                $('#edit_name').val(btn.data('name') || '');
                $('#edit_mobile').val(btn.data('mobile') || '');
                $('#edit_email').val(btn.data('email') || '');
                $('#edit_password').val('');
                $('#edit_password_confirmation').val('');

                const isActive = btn.data('is-active');
                $('#edit_is_active').prop('checked', isActive == 1 || isActive === true || isActive ===
                    '1');

                $('.edit-company-checkbox').prop('checked', false);
                $('.company-roles-wrapper').hide();

                const userCompanies = btn.data('companies') || [];
                const userCompanyRoles = btn.data('company-roles') || {};

                userCompanies.forEach(companyId => {
                    $(`#edit_company_${companyId}`).prop('checked', true);
                    $(`#edit_company_roles_${companyId}`).show();
                    const roleIds = userCompanyRoles[companyId] || [];
                    if (roleIds.length > 0) {
                        $(`#edit_company_roles_${companyId} select`).val(roleIds).trigger('change');
                    }
                });

                updateDefaultSelect('.edit-company-checkbox', '#edit_default_company');
                const defaultCompanyId = btn.data('default-company');
                if (defaultCompanyId) {
                    $('#edit_default_company').val(defaultCompanyId).trigger('change');
                }

                $('#editUserModal').modal('show');
            });

            // ========== حذف کاربر با استفاده از toast سراسری ==========
            $(document).on('click', '.delete-user-btn', function() {
                const btn = $(this);
                const url = btn.data('url');
                const name = btn.data('name');
                const userId = btn.data('id');
                const currentUserId = '{{ auth()->id() }}';

                if (parseInt(userId) === parseInt(currentUserId)) {
                    if (typeof showToast !== 'undefined') {
                        showToast('شما نمی‌توانید کاربری خود را حذف کنید!', 'error', 'خطا');
                    } else {
                        alert('شما نمی‌توانید کاربری خود را حذف کنید!');
                    }
                    return;
                }

                if (typeof Swal !== 'undefined') {
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
                } else {
                    if (confirm(`کاربر "${name}" حذف شود؟`)) {
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
                }
            });

            // ========== پاک کردن فرم مودال‌ها ==========
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
        });
    </script>

    {{-- نمایش خطاهای اعتبارسنجی در مودال --}}
    @if ($errors->any() && session('show_create_modal'))
        <script>
            $(function() {
                // باز کردن مودال
                $('#createUserModal').modal('show');

                // نمایش خطاها یکی یکی با toast
                @foreach ($errors->all() as $error)
                    setTimeout(function() {
                        if (typeof showToast !== 'undefined') {
                            showToast('{{ $error }}', 'error', 'خطای اعتبارسنجی');
                        } else {
                            alert('{{ $error }}');
                        }
                    }, {{ $loop->index * 500 }});
                @endforeach
            });
        </script>
    @endif

    <script>
    // ========== باز کردن خودکار مودال ایجاد اگر خطا وجود داشته باشد ==========
    @if($errors->any() && session('show_create_modal'))
        $(function() {
            // باز کردن مودال با تنظیمات صحیح
            var createModal = new bootstrap.Modal(document.getElementById('createUserModal'), {
                backdrop: 'static',
                keyboard: false
            });
            createModal.show();
            
            // اسکرول به بالای مودال
            setTimeout(function() {
                $('#createUserModal .modal-body').animate({ scrollTop: 0 }, 300);
            }, 200);
        });
    @endif
    
    // ========== باز کردن خودکار مودال ویرایش اگر خطا وجود داشته باشد ==========
    @if($errors->any() && session('show_edit_modal'))
        $(function() {
            var editModal = new bootstrap.Modal(document.getElementById('editUserModal'), {
                backdrop: 'static',
                keyboard: false
            });
            editModal.show();
            
            setTimeout(function() {
                $('#editUserModal .modal-body').animate({ scrollTop: 0 }, 300);
            }, 200);
        });
    @endif
    
    // ========== ایمپورت فایل با AJAX ==========
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader bx-spin"></i> در حال ایمپورت...');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast(response.message || 'ایمپورت با موفقیت انجام شد', 'success');
                    $('#importModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, messages) {
                        $.each(messages, function(i, message) {
                            showToast(message, 'error');
                        });
                    });
                } else if (xhr.status === 403) {
                    showToast('شما مجوز انجام این عملیات را ندارید', 'error');
                } else {
                    showToast('خطا در ایمپورت فایل', 'error');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('شروع ایمپورت');
            }
        });
    });
</script>
@endpush
