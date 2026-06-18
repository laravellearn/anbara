@extends('layouts.master')

@section('title', 'انبارها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل انبارها</span>
                            <h3 class="mb-0 mt-1">{{ $warehouses->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-buildings bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-buildings me-1"></i> لیست انبارها
                <small class="text-muted ms-2">({{ $warehouses->total() }})</small>
            </h5>
            @can('access', 'warehouses.create')
            <a href="{{ route('warehouse.warehouses.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> انبار جدید
            </a>
            @endcan
        </div>

        <div class="table-responsive">
            @include('warehouse.warehouses._table', ['warehouses' => $warehouses])
        </div>
    </div>
</div>
@endsection