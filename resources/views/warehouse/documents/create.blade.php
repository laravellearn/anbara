@extends('layouts.master')
@section('title', 'سند انبار جدید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card shadow-none border">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bx bx-plus-circle me-1"></i> ثبت سند جدید —
                <span class="badge bg-label-{{ \App\Models\WarehouseDocument::typeColors()[$defaultType ?? 'receipt'] }}">
                    {{ \App\Models\WarehouseDocument::typeLabels()[$defaultType ?? 'receipt'] }}
                </span>
            </h5>
            <a href="{{ route('warehouse.documents.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> بازگشت
            </a>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show m-3 mb-0">
            <strong><i class="bx bx-error-circle me-1"></i> خطا:</strong>
            <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('warehouse.documents.store') }}" method="POST" id="documentForm">
            @csrf
            @include('warehouse.documents._form', [
                'document'    => (object)['type' => $defaultType, 'items' => collect(), 'warehouse_id' => null,
                    'destination_warehouse_id' => null, 'warehouse_location_id' => null,
                    'contact_id' => null, 'fiscal_year_id' => null, 'cost_center_id' => null,
                    'document_date' => null, 'reference_number' => null, 'description' => null],
                'isCreate'    => true,
                'defaultType' => $defaultType,
            ])
            <div class="card-footer text-end">
                <a href="{{ route('warehouse.documents.index') }}" class="btn btn-label-secondary me-2">انصراف</a>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره پیش‌نویس</button>
            </div>
        </form>
    </div>
</div>
@endsection
