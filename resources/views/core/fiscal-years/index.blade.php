@extends('layouts.master')

@section('title', 'سال‌های مالی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- کارت‌های آماری --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل سال‌های مالی</span>
                            <h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-calendar bx-sm"></i>
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
                            <span class="fw-medium text-muted">سال مالی فعال</span>
                            <h3 class="mb-0 mt-1">{{ $stats['current'] }}</h3>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="bx bx-check-circle bx-sm"></i>
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
                            <span class="fw-medium text-muted">سال‌های فعال</span>
                            <h3 class="mb-0 mt-1">{{ $stats['active'] }}</h3>
                        </div>
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="bx bx-play bx-sm"></i>
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
                            <span class="fw-medium text-muted">سال‌های بسته شده</span>
                            <h3 class="mb-0 mt-1">{{ $stats['closed'] }}</h3>
                        </div>
                        <span class="badge bg-label-danger rounded p-2">
                            <i class="bx bx-lock bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- نوار جستجو/فیلتر --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium"><i class="bx bx-search-alt me-1"></i>جستجو</label>
                    <input type="text" id="liveSearch" class="form-control" placeholder="نام سال مالی..." autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">وضعیت</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">همه</option>
                        <option value="active">فعال</option>
                        <option value="closed">بسته شده</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">نمایش</label>
                    <select id="perPage" class="form-select">
                        <option value="10">۱۰</option>
                        <option value="20" selected>۲۰</option>
                        <option value="50">۵۰</option>
                        <option value="100">۱۰۰</option>
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

    {{-- جدول --}}
    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-calendar me-1"></i>لیست سال‌های مالی
                <small class="text-muted ms-2" id="filteredCount">({{ $fiscalYears->total() }} سال)</small>
            </h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFiscalYearModal">
                <i class="bx bx-plus"></i> سال مالی جدید
            </button>
        </div>

        <div class="table-responsive" id="fiscalYearsTableWrapper">
            @include('core.fiscal-years._table', ['fiscalYears' => $fiscalYears])
        </div>
    </div>
</div>

@include('core.fiscal-years._modal')

@endsection

@push('scripts')
<script>
    $(function() {
        let currentSort = 'created_at';
        let currentDirection = 'desc';

        function performSearch() {
            const search = $('#liveSearch').val();
            const status = $('#filterStatus').val();
            const perPage = $('#perPage').val();

            $('#fiscalYearsTableWrapper').addClass('opacity-50');

            $.ajax({
                url: '{{ route('fiscal-years.index') }}',
                data: {
                    search: search,
                    status: status,
                    per_page: perPage,
                    sort: currentSort,
                    direction: currentDirection,
                },
                success: function(response) {
                    $('#fiscalYearsTableWrapper').html(response.html);
                    $('#fiscalYearsTableWrapper').removeClass('opacity-50');
                    $('#filteredCount').text(`(${response.total} سال)`);
                },
                error: function() {
                    $('#fiscalYearsTableWrapper').removeClass('opacity-50');
                    if (typeof showToast !== 'undefined') {
                        showToast('خطا در جستجو', 'error');
                    }
                }
            });
        }

        let searchTimeout;
        $('#liveSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });

        $('#filterStatus, #perPage').on('change', performSearch);

        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterStatus').val('');
            $('#perPage').val('20');
            currentSort = 'created_at';
            currentDirection = 'desc';
            performSearch();
        });

        // مرتب‌سازی
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
            $('.sort-icon').removeClass('active bx-sort-up bx-sort-down text-primary').addClass('bx-sort text-muted');
            const $activeHeader = $(`.sortable[data-sort="${currentSort}"]`);
            const $icon = $activeHeader.find('.sort-icon');
            $icon.removeClass('bx-sort text-muted').addClass('active text-primary');
            $icon.addClass(currentDirection === 'asc' ? 'bx-sort-up' : 'bx-sort-down');
        }

        // مودال ویرایش - پر کردن داده‌ها
        $(document).on('click', '.edit-fy-btn', function() {
            const btn = $(this);
            $('#edit_fiscal_year_id').val(btn.data('id'));
            $('#edit_name').val(btn.data('name'));
            $('#edit_start_date').val(btn.data('start-date'));
            $('#edit_end_date').val(btn.data('end-date'));
            $('#edit_is_active').prop('checked', btn.data('is-active') == 1);
            $('#editFiscalYearModal').modal('show');
        });

        // پاک کردن فرم‌ها هنگام بسته شدن مودال
        $('#createFiscalYearModal, #editFiscalYearModal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    });
</script>
@endpush