@extends('layouts.master')

@section('title', 'لیست کالاها')


@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- کارت‌های آماری --}}
    <div class="row g-4 mb-4" id="statsCards">
        @include('warehouse.products._stats', ['stats' => $stats])
    </div>

    {{-- جستجوی زنده + فیلترها --}}
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
                    <label class="form-label fw-medium">برند</label>
                    <select id="filterBrand" class="form-select select2">
                        <option value="">همه</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}">{{ $brand->title }}</option>
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

    {{-- لیست کالاها --}}
    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-package me-1"></i> لیست کالاها
                <small class="text-muted ms-2" id="filteredCount">({{ $products->total() }})</small>
            </h5>
            @can('access', 'products.create')
            <a href="{{ route('warehouse.products.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> کالای جدید
            </a>
            @endcan
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
            const brand = $('#filterBrand').val();
            const status = $('#filterStatus').val();

            $tableWrapper.addClass('opacity-50');

            $.ajax({
                url: '{{ route('
                warehouse.products.index ') }}',
                data: {
                    search: search,
                    category_id: category,
                    brand_id: brand,
                    status: status,
                    ajax: 1,
                },
                success: function(response) {
                    $tableWrapper.html(response.html);
                    $tableWrapper.removeClass('opacity-50');
                    $filteredCount.text(`(${response.total})`);

                    // به‌روزرسانی کارت‌های آماری
                    $statsCards.html(response.statsHtml);
                },
                error: function() {
                    $tableWrapper.removeClass('opacity-50');
                    // نمایش خطا
                }
            });
        }

        $('#liveSearch').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });

        $('#filterCategory, #filterBrand, #filterStatus').on('change', performSearch);

        $('#clearSearch').on('click', function() {
            $('#liveSearch').val('').focus();
            performSearch();
        });

        $('#resetFilters').on('click', function() {
            $('#liveSearch').val('');
            $('#filterCategory').val('').trigger('change');
            $('#filterBrand').val('').trigger('change');
            $('#filterStatus').val('');
            performSearch();
        });
    });


    let unitIndex = {
        {
            isset($product) ? $product - > measurementUnits - > count() : 0
        }
    };
    $('#add-unit').click(function() {
        const row = `<div class="row mb-2 unit-row">
        <div class="col-5">
            <select name="measurement_units[${unitIndex}][id]" class="form-select">
                <option value="">انتخاب واحد</option>
                @foreach($measurementUnits as $mu)
                    <option value="{{ $mu->id }}">{{ $mu->title }} ({{ $mu->symbol }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-4">
            <input type="number" step="any" name="measurement_units[${unitIndex}][conversion_factor]" class="form-control" value="1">
        </div>
        <div class="col-2">
            <div class="form-check mt-2">
                <input type="checkbox" name="measurement_units[${unitIndex}][is_default]" value="1" class="form-check-input">
                <label class="form-check-label">پیش‌فرض</label>
            </div>
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-sm btn-danger remove-unit"><i class="bx bx-x"></i></button>
        </div>
    </div>`;
        $('#additional-units').append(row);
        unitIndex++;
    });
    $(document).on('click', '.remove-unit', function() {
        $(this).closest('.unit-row').remove();
    });


    $('#product_type_id').on('change', function() {
        const typeId = $(this).val();
        const productId = '{{ $product->id ?? '
        ' }}';
        if (typeId) {
            $.get('{{ route('
                warehouse.product - types.attributes ', ': typeId ') }}'.replace(':typeId', typeId), {
                    product_id: productId
                },
                function(response) {
                    $('#dynamic-attributes').html(response.html);
                });
        } else {
            $('#dynamic-attributes').html('');
        }
    });

    // در صورت ویرایش، اگر نوع کالا از قبل انتخاب شده باشد، بارگذاری اولیه انجام شود
    @if(isset($product) && $product - > product_type_id)
    $('#product_type_id').trigger('change');
    @endif
</script>
@endpush