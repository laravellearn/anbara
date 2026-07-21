@extends('layouts.master')
@section('title', 'تراکنش‌های انبار')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4 mb-4" id="statsCards">
        @include('warehouse.stock-transactions._stats', ['stats' => $stats])
    </div>

    {{-- فیلترها --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-medium">جستجو (کالا / SKU)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bx bx-search-alt"></i></span>
                        <input type="text" id="liveSearch" class="form-control" placeholder="نام کالا یا کد SKU..." autocomplete="off">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">نوع</label>
                    <select id="filterType" class="form-select">
                        <option value="">همه</option>
                        @foreach(\App\Enums\InventoryTransactionType::cases() as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-medium">وضعیت</label>
                    <select id="filterStatus" class="form-select">
                        <option value="">همه</option>
                        @foreach(\App\Enums\InventoryTransactionStatus::cases() as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
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
                <div class="col-md-2 d-grid">
                    <button id="btnReset" class="btn btn-outline-secondary">
                        <i class="bx bx-reset me-1"></i> پاک کردن
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-transfer me-1"></i> تراکنش‌های انبار
                <small class="text-muted ms-2" id="filteredCount">({{ $transactions->total() }})</small>
            </h5>
            @can('access', 'stock-transactions.create')
            <a href="{{ route('warehouse.stock-transactions.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> تراکنش جدید
            </a>
            @endcan
        </div>
        <div class="table-responsive" id="tableWrapper">
            @include('warehouse.stock-transactions._table', ['transactions' => $transactions])
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    let timeout;
    const $wrapper = $('#tableWrapper');
    const $count   = $('#filteredCount');

    function load() {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            const params = {
                ajax: 1,
                search:       $('#liveSearch').val(),
                type:         $('#filterType').val(),
                status:       $('#filterStatus').val(),
                warehouse_id: $('#filterWarehouse').val(),
            };
            $wrapper.addClass('opacity-50');
            $.get('{{ route("warehouse.stock-transactions.index") }}', params, function (res) {
                $wrapper.html(res.html).removeClass('opacity-50');
                $count.text('(' + res.total + ')');
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
});
</script>
@endpush
