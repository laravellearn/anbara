@extends('layouts.master')

@section('title', 'لیست کالاها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کل کالاها</span>
                            <h3 class="mb-0 mt-1">{{ $products->total() }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="bx bx-package bx-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h5 class="card-title mb-0">
                <i class="bx bx-package me-1"></i> لیست کالاها
                <small class="text-muted ms-2">({{ $products->total() }})</small>
            </h5>
            @can('access', 'products.create')
            <a href="{{ route('warehouse.products.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus"></i> کالای جدید
            </a>
            @endcan
        </div>

        <div class="table-responsive">
            @include('warehouse.products._table', ['products' => $products])
        </div>
    </div>
</div>
@endsection