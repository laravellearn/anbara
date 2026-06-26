@extends('layouts.master')

@section('title', 'واحدهای سازمانی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('core.organizational-units._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجوی زنده</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="نام یا کد..." autocomplete="off">
                        <span class="input-group-text bg-white" id="clearSearch" style="cursor: pointer;"><i class="bx bx-x"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">وضعیت</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">همه</option>
                        <option value="active">فعال</option>
                        <option value="inactive">غیرفعال</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                <i class="bx bx-sitemap me-1"></i> واحدهای سازمانی
                <small class="text-muted ms-2" id="filteredCount">({{ $units->total() }})</small>
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                @can('access', 'organizational-units.create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bx bx-plus"></i> واحد جدید
                </button>
                @endcan
            </div>
        </div>

        <div class="table-responsive" id="tableWrapper">
            @include('core.organizational-units._table', ['units' => $units])
        </div>
    </div>
</div>

@include('core.organizational-units._modal', ['allUnits' => $allUnits, 'users' => $users])
@endsection

@push('scripts')
<script>
    $(function(){
        let searchTimeout;
        const $tableWrapper = $('#tableWrapper');
        const $statsCards = $('#statsCards');
        const $filteredCount = $('#filteredCount');

        function performSearch() {
            const search = $('#liveSearch').val();
            const status = $('#filterStatus').val();
            $tableWrapper.addClass('opacity-50');
            $.ajax({
                url: '{{ route('organizational-units.index') }}',
                data: { search, status, ajax: 1 },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $statsCards.html(response.statsHtml);
                    $filteredCount.text(`(${response.total})`);
                }
            }).always(() => $tableWrapper.removeClass('opacity-50'));
        }

        $('#liveSearch').on('keyup', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(performSearch, 500); });
        $('#filterStatus').on('change', performSearch);
        $('#clearSearch').on('click', function() { $('#liveSearch').val('').focus(); performSearch(); });
        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterStatus').val('');
            performSearch();
        });

        // ========== مودال ویرایش ==========
        $(document).on('click', '.edit-unit-btn', function() {
            const btn = $(this);
            const id = btn.data('id');
            $('#editUnitForm').attr('action', `{{ route('organizational-units.update', ':id') }}`.replace(':id', id));
            $('#edit_unit_name').val(btn.data('name'));
            $('#edit_unit_code').val(btn.data('code'));
            $('#edit_unit_parent').val(btn.data('parent'));
            $('#edit_unit_manager').val(btn.data('manager'));
            $('#edit_unit_desc').val(btn.data('desc'));
            $('#edit_unit_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#editModal').modal('show');
        });

        // ریست فرم ایجاد
        $('#createModal').on('hidden.bs.modal', function() {
            $('#unitForm')[0].reset();
            $('input[name="_method"]').remove();
        });

        // ریست فرم ویرایش
        $('#editModal').on('hidden.bs.modal', function() {
            $('#editUnitForm')[0].reset();
            $('#editUnitForm').attr('action', '{{ route('organizational-units.update', ':id') }}');
        });

        // حذف با تأیید
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این واحد سازمانی حذف خواهد شد.",
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

        // نمایش خطاهای مودال‌ها
        @if($errors->any() && session('show_create_modal'))
            $('#createModal').modal('show');
        @endif
        @if($errors->any() && session('show_edit_modal'))
            $('#editModal').modal('show');
        @endif
    });
</script>
@endpush