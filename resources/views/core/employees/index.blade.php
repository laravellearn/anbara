@extends('layouts.master')

@section('title', 'کارمندان')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('core.employees._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجوی زنده</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="نام، کد یا موبایل..." autocomplete="off">
                        <span class="input-group-text bg-white" id="clearSearch" style="cursor: pointer;"><i class="bx bx-x"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">واحد سازمانی</label>
                    <select id="filterUnit" class="form-select select2">
                        <option value="">همه</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">وضعیت</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">همه</option>
                        <option value="active">فعال</option>
                        <option value="inactive">غیرفعال</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-secondary w-100" id="resetFilters">
                        <i class="bx bx-reset"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-user-badge me-1"></i> کارمندان
                <small class="text-muted ms-2" id="filteredCount">({{ $employees->total() }})</small>
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export"></i> خروجی
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bx-file me-1"></i> Excel (به‌زودی)</a></li>
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bxs-file-pdf me-1"></i> PDF (به‌زودی)</a></li>
                    </ul>
                </div>

                @can('access', 'employees.create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bx bx-plus"></i> کارمند جدید
                </button>
                @endcan
            </div>
        </div>

        <div class="table-responsive" id="tableWrapper">
            @include('core.employees._table', ['employees' => $employees])
        </div>
    </div>
</div>

@include('core.employees._modal', ['units' => $units, 'users' => $users])
@endsection

@push('scripts')
<script>

// نمایش/مخفی فیلدهای کاربر در مودال ایجاد
$('#create_user_check').on('change', function() {
    $('#createUserFields').toggle(this.checked);
});

// نمایش/مخفی فیلدهای کاربر در مودال ویرایش
$('#edit_create_user_check').on('change', function() {
    $('#editUserFields').toggle(this.checked);
});

$(function(){
        let searchTimeout;
        const $tableWrapper = $('#tableWrapper');
        const $statsCards = $('#statsCards');
        const $filteredCount = $('#filteredCount');

        function performSearch() {
            const search = $('#liveSearch').val();
            const unit = $('#filterUnit').val();
            const status = $('#filterStatus').val();
            $tableWrapper.addClass('opacity-50');
            $.ajax({
                url: '{{ route('employees.index') }}',
                data: { search, organizational_unit_id: unit, status, ajax: 1 },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $statsCards.html(response.statsHtml);
                    $filteredCount.text(`(${response.total})`);
                }
            }).always(() => $tableWrapper.removeClass('opacity-50'));
        }

        $('#liveSearch').on('keyup', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(performSearch, 500); });
        $('#filterUnit, #filterStatus').on('change', performSearch);
        $('#clearSearch').on('click', function() { $('#liveSearch').val('').focus(); performSearch(); });
        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterUnit').val('').trigger('change');
            $('#filterStatus').val('');
            performSearch();
        });

        // ویرایش: باز کردن مودال ویرایش با داده‌ها
        $(document).on('click', '.edit-employee-btn', function() {
            const btn = $(this);
            const id = btn.data('id');
            $('#editEmployeeForm').attr('action', '{{ route('employees.update', ':id') }}'.replace(':id', id));
            $('#edit_emp_name').val(btn.data('name'));
            $('#edit_emp_code').val(btn.data('code'));
            $('#edit_emp_national_code').val(btn.data('national_code'));
            $('#edit_emp_unit').val(btn.data('unit'));
            $('#edit_emp_user').val(btn.data('user'));
            $('#edit_emp_position').val(btn.data('position'));
            $('#edit_emp_mobile').val(btn.data('mobile'));
            $('#edit_emp_phone').val(btn.data('phone'));
            $('#edit_emp_email').val(btn.data('email'));
            $('#edit_emp_employment_date').val(btn.data('employment_date'));
            $('#edit_emp_address').val(btn.data('address'));
            $('#edit_emp_desc').val(btn.data('desc'));
            $('#edit_emp_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#editModal').modal('show');
        });

        // ریست فرم‌ها
        $('#createModal').on('hidden.bs.modal', function() {
            $('#employeeForm')[0].reset();
        });
        $('#editModal').on('hidden.bs.modal', function() {
            $('#editEmployeeForm')[0].reset();
        });

        // حذف با تأیید
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این کارمند حذف خواهد شد.",
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
                    form.submit();
                }
            });
        });

        @if($errors->any() && session('show_create_modal'))
            $('#createModal').modal('show');
        @endif
        @if($errors->any() && session('show_edit_modal'))
            $('#editModal').modal('show');
        @endif
    });
</script>
@endpush