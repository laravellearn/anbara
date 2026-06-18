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
            @can('access', 'employees.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> کارمند جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive" id="tableWrapper">
            @include('core.employees._table', ['employees' => $employees])
        </div>
    </div>
</div>

@include('core.employees._modal', ['units' => $units])
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
            const unit = $('#filterUnit').val();
            const status = $('#filterStatus').val();
            $tableWrapper.addClass('opacity-50');
            $.ajax({
                url: '{{ route('warehouse.employees.index') }}',
                data: { search, unit_id: unit, status, ajax: 1 },
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
    });
</script>
@endpush