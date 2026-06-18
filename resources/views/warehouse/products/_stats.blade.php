{{-- کارت‌های آماری --}}
@php
    $stats = $stats ?? ['total'=>0, 'active'=>0, 'inactive'=>0, 'low_stock'=>0];
@endphp
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">کل کالاها</span>
                    <h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <span class="badge bg-label-primary rounded p-2">
                    <i class="bx bx-package bx-sm"></i>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">کالاهای فعال</span>
                    <h3 class="mb-0 mt-1">{{ $stats['active'] }}</h3>
                </div>
                <span class="badge bg-label-success rounded p-2">
                    <i class="bx bx-check-circle bx-sm"></i>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">کالاهای غیرفعال</span>
                    <h3 class="mb-0 mt-1">{{ $stats['inactive'] }}</h3>
                </div>
                <span class="badge bg-label-danger rounded p-2">
                    <i class="bx bx-x-circle bx-sm"></i>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">موجودی بحرانی</span>
                    <h3 class="mb-0 mt-1">{{ $stats['low_stock'] }}</h3>
                </div>
                <span class="badge bg-label-warning rounded p-2">
                    <i class="bx bx-error bx-sm"></i>
                </span>
            </div>
        </div>
    </div>
</div>