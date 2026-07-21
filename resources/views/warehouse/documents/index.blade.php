@extends('layouts.master')
@section('title', 'اسناد انبار')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('warehouse.documents._stats', ['stats' => $stats])
    </div>

    {{-- فیلترها --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-medium">جستجو</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="شماره سند / مرجع / توضیحات...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">نوع سند</label>
                    <select id="filterType" class="form-select">
                        <option value="">همه انواع</option>
                        @foreach(\App\Models\WarehouseDocument::typeLabels() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">وضعیت</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">همه</option>
                        @foreach(\App\Models\WarehouseDocument::statusLabels() as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">انبار</label>
                    <select id="filterWarehouse" class="form-select">
                        <option value="">همه</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button id="btnReset" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></button>
                </div>
                <div class="col-md-2 text-end">
                    @can('access', 'warehouse-documents.create')
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bx bx-plus me-1"></i> سند جدید
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach(\App\Models\WarehouseDocument::typeLabels() as $type => $label)
                            <li>
                                <a class="dropdown-item" href="{{ route('warehouse.documents.create', ['type' => $type]) }}">
                                    <span class="badge bg-label-{{ \App\Models\WarehouseDocument::typeColors()[$type] }} me-2">{{ $label }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-file me-1"></i> اسناد انبار <small class="text-muted ms-2" id="filteredCount">({{ $documents->total() }})</small></h5>
        </div>
        <div class="table-responsive" id="tableWrapper">
            @include('warehouse.documents._table', ['documents' => $documents])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    let timeout;
    function load() {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            $('#tableWrapper').addClass('opacity-50');
            $.get('{{ route("warehouse.documents.index") }}', {
                ajax: 1,
                search:       $('#liveSearch').val(),
                type:         $('#filterType').val(),
                status:       $('#filterStatus').val(),
                warehouse_id: $('#filterWarehouse').val(),
            }, function (res) {
                $('#tableWrapper').html(res.html).removeClass('opacity-50');
                $('#filteredCount').text('(' + res.total + ')');
                $('#statsCards').html(res.statsHtml);
            });
        }, 350);
    }
    $('#liveSearch').on('input', load);
    $('#filterType, #filterStatus, #filterWarehouse').on('change', load);
    $('#btnReset').on('click', function () {
        $('#liveSearch').val('');
        $('#filterType, #filterStatus, #filterWarehouse').val('');
        load();
    });
    $(document).on('submit', '.delete-form', function (e) {
        e.preventDefault();
        if (confirm('آیا از حذف این سند مطمئن هستید؟')) this.submit();
    });
});
</script>
@endpush
