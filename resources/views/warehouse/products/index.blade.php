@extends('layouts.master')

@section('title', 'لیست کالاها')


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('warehouse.products._stats', ['stats' => $stats])
    </div>

    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجوی زنده</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="عنوان، SKU یا بارکد..." autocomplete="off">
                        <span class="input-group-text bg-white cursor-pointer" id="clearSearch" style="cursor: pointer;">
                            <i class="bx bx-x"></i>
                        </span>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">دسته‌بندی</label>
                    <select id="filterCategory" class="form-select select2">
                        <option value="">همه</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
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
                <i class="bx bx-package me-1"></i> لیست کالاها
                <small class="text-muted ms-2" id="filteredCount">({{ $products->total() }})</small>
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

                @can('access', 'products.create')
                <a href="{{ route('warehouse.products.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus"></i> کالای جدید
                </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive" id="productsTableWrapper">
            @include('warehouse.products._table', ['products' => $products])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        let searchTimeout;
        const $tableWrapper = $('#productsTableWrapper');
        const $statsCards = $('#statsCards');
        const $filteredCount = $('#filteredCount');

        function performSearch() {
            const search = $('#liveSearch').val();
            const category = $('#filterCategory').val();
            const status = $('#filterStatus').val();

            $tableWrapper.addClass('opacity-50');

            $.ajax({
                url: '{{ route('warehouse.products.index') }}',
                data: {
                    search: search,
                    category_id: category,
                    status: status,
                    ajax: 1,
                },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $statsCards.html(response.statsHtml);
                    $filteredCount.text(`(${response.total})`);
                },
                error: function() {
                    showToast('خطا در جستجو', 'error');
                }
            }).always(() => $tableWrapper.removeClass('opacity-50'));
        }

        $('#liveSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });

        $('#filterCategory, #filterStatus').on('change', performSearch);

        $('#clearSearch').on('click', function() {
            $('#liveSearch').val('').focus();
            performSearch();
        });

        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterCategory').val('').trigger('change');
            $('#filterStatus').val('');
            performSearch();
        });

        // حذف با تأیید
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: "این کالا حذف خواهد شد.",
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
    });
</script>
@endpush