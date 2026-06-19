@extends('layouts.master')

@section('title', 'مدیریت سازمان‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- کارت‌های آماری --}}
    @include('core.companies._stats')

    {{-- فیلترها --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجو</label>
                    <input type="text" id="liveSearch" class="form-control" placeholder="نام سازمان..." autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">وضعیت</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">همه</option>
                        <option value="active">فعال</option>
                        <option value="inactive">غیرفعال</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">سازمان مادر</label>
                    <select id="filterParent" class="form-select">
                        <option value="">همه</option>
                        @foreach($parentCompanies as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                        @endforeach
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
                <i class="bx bx-buildings me-1"></i>لیست سازمان‌ها
                <small class="text-muted ms-2" id="filteredCount">({{ $companies->total() }} سازمان)</small>
            </h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCompanyModal">
                <i class="bx bx-plus"></i> سازمان جدید
            </button>
        </div>

        <div class="table-responsive" id="companiesTableWrapper">
            @include('core.companies._table', ['companies' => $companies])
        </div>
    </div>
</div>

@include('core.companies._modals')

@endsection

@push('scripts')
<script>
    $(function() {
        let currentSort = 'created_at';
        let currentDirection = 'desc';

        function performSearch() {
            const search = $('#liveSearch').val();
            const status = $('#filterStatus').val();
            const parentId = $('#filterParent').val();
            const perPage = $('#perPage').val();

            $('#companiesTableWrapper').addClass('opacity-50');

            $.ajax({
                url: '{{ route('companies.index') }}',
                data: {
                    search: search,
                    status: status,
                    parent_id: parentId,
                    per_page: perPage,
                    sort: currentSort,
                    direction: currentDirection,
                },
                success: function(response) {
                    $('#companiesTableWrapper').html(response.html);
                    $('#companiesTableWrapper').removeClass('opacity-50');
                    $('#filteredCount').text(`(${response.total} سازمان)`);
                },
                error: function() {
                    $('#companiesTableWrapper').removeClass('opacity-50');
                }
            });
        }

        let searchTimeout;
        $('#liveSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });

        $('#filterStatus, #filterParent, #perPage').on('change', performSearch);

        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterStatus').val('');
            $('#filterParent').val('');
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

        // باز کردن مودال ویرایش
        $(document).on('click', '.edit-company-btn', function() {
            const btn = $(this);
            $('#edit_company_id').val(btn.data('id'));
            $('#edit_title').val(btn.data('title'));
            $('#edit_description').val(btn.data('description'));
            $('#edit_parent_id').val(btn.data('parent-id') || '');
            $('#edit_is_active').prop('checked', btn.data('is-active') == 1);
            $('#editCompanyModal').modal('show');
        });
    });
</script>
@endpush