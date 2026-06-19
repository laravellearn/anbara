@extends('layouts.master')

@section('title', 'دسته‌بندی‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('warehouse.categories._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجوی زنده</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="عنوان..." autocomplete="off">
                        <span class="input-group-text bg-white" id="clearSearch" style="cursor: pointer;"><i class="bx bx-x"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">والد</label>
                    <select id="filterParent" class="form-select select2">
                        <option value="">همه</option>
                        @foreach($allCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->title }}</option>
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
                <i class="bx bx-category me-1"></i> دسته‌بندی‌ها
                <small class="text-muted ms-2" id="filteredCount">({{ $categories->total() }})</small>
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                {{-- Export placeholder --}}
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-export"></i> خروجی
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bx-file me-1"></i> Excel (به‌زودی)</a></li>
                        <li><a class="dropdown-item disabled" href="#"><i class="bx bxs-file-pdf me-1"></i> PDF (به‌زودی)</a></li>
                    </ul>
                </div>

                @can('access', 'product-categories.create')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bx bx-plus"></i> دسته‌بندی جدید
                </button>
                @endcan
            </div>
        </div>

        <div class="table-responsive" id="categoriesTableWrapper">
            @include('warehouse.categories._table', ['categories' => $categories])
        </div>
    </div>
</div>

@include('warehouse.categories._modal', ['allCategories' => $allCategories])
@endsection

@push('scripts')
<script>
    $(function(){
        let searchTimeout;
        const $tableWrapper = $('#categoriesTableWrapper');
        const $statsCards = $('#statsCards');
        const $filteredCount = $('#filteredCount');

        function performSearch() {
            const search = $('#liveSearch').val();
            const parent = $('#filterParent').val();
            const status = $('#filterStatus').val();

            $tableWrapper.addClass('opacity-50');
            $.ajax({
                url: '{{ route('warehouse.categories.index') }}',
                data: { search, parent_id: parent, status, ajax: 1 },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $statsCards.html(response.statsHtml);
                    $filteredCount.text(`(${response.total})`);
                },
                error: function() { /* مدیریت خطا */ }
            }).always(() => $tableWrapper.removeClass('opacity-50'));
        }

        $('#liveSearch').on('keyup', function() { clearTimeout(searchTimeout); searchTimeout = setTimeout(performSearch, 500); });
        $('#filterParent, #filterStatus').on('change', performSearch);
        $('#clearSearch').on('click', function() { $('#liveSearch').val('').focus(); performSearch(); });
        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterParent').val('').trigger('change');
            $('#filterStatus').val('');
            performSearch();
        });

        // ========== مودال ویرایش ==========
        $(document).on('click', '.edit-cat-btn', function() {
            const btn = $(this);
            const id = btn.data('id');
            $('#catForm').attr('action', `{{ route('warehouse.categories.update', ':id') }}`.replace(':id', id));
            if (!$('input[name="_method"]').length) $('#catForm').prepend('<input type="hidden" name="_method" value="PUT">');
            $('#cat_title').val(btn.data('title'));
            $('#cat_parent').val(btn.data('parent'));
            $('#cat_desc').val(btn.data('desc'));
            $('#cat_active').prop('checked', btn.data('active') == '1' || btn.data('active') == true);
            $('#createModal').modal('show');
        });

        // ========== ریست فرم هنگام بسته شدن مودال ==========
        $('#createModal').on('hidden.bs.modal', function() {
            $('#catForm').attr('action', `{{ route('warehouse.categories.store') }}`);
            $('input[name="_method"]').remove();
            $('#catForm')[0].reset();
        });

        // ========== حذف با تأیید ==========
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این دسته‌بندی حذف خواهد شد.",
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

        // ========== نمایش خطاهای اعتبارسنجی در مودال ==========
        @if($errors->any() && session('show_create_modal'))
            $('#createModal').modal('show');
            @foreach ($errors->all() as $error)
                if (typeof showToast !== 'undefined') {
                    showToast('{{ $error }}', 'error', 'خطای اعتبارسنجی');
                }
            @endforeach
        @endif
    });
</script>
@endpush