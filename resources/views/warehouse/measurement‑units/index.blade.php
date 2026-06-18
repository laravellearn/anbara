@extends('layouts.master')

@section('title', 'واحدهای اندازه‌گیری')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل واحدها</span>
                            <h3 class="mb-0 mt-1">{{ $units->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-ruler bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-ruler me-1"></i> لیست واحدهای اندازه‌گیری
                <small class="text-muted ms-2">({{ $units->total() }} واحد)</small>
            </h5>
            @can('access', 'measurement-units.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bx bx-plus"></i> واحد جدید
            </button>
            @endcan
        </div>

        <div class="table-responsive" id="unitsTableWrapper">
            @include('warehouse.measurement-units._table', ['units' => $units])
        </div>
    </div>
</div>

@include('warehouse.measurement-units._modal', ['allUnits' => $allUnits])
@endsection

@push('scripts')
<script>
    $(function(){
        $('.edit-unit-btn').on('click', function(){
            // مشابه الگوی کاربران
        });
        // حذف و سایر اسکریپت‌ها
    });
</script>
@endpush