<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-none border">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span class="fw-medium text-muted">کل سازمان‌ها</span>
                        <h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3>
                    </div>
                    <span class="badge bg-label-primary rounded p-2">
                        <i class="bx bx-buildings bx-sm"></i>
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
                        <span class="fw-medium text-muted">فعال</span>
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
                        <span class="fw-medium text-muted">غیرفعال</span>
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
                        <span class="fw-medium text-muted">سازمان‌های مادر</span>
                        <h3 class="mb-0 mt-1">{{ $stats['parents'] }}</h3>
                    </div>
                    <span class="badge bg-label-warning rounded p-2">
                        <i class="bx bx-network-chart bx-sm"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>