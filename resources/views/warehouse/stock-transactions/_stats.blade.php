<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="fw-medium text-muted">کل تراکنش‌ها</span>
                    <h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <span class="badge bg-label-primary rounded p-2"><i class="bx bx-transfer bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="fw-medium text-muted">پیش‌نویس</span>
                    <h3 class="mb-0 mt-1">{{ $stats['draft'] }}</h3>
                </div>
                <span class="badge bg-label-secondary rounded p-2"><i class="bx bx-edit bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="fw-medium text-muted">در انتظار تأیید</span>
                    <h3 class="mb-0 mt-1">{{ $stats['pending'] }}</h3>
                </div>
                <span class="badge bg-label-warning rounded p-2"><i class="bx bx-time bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <span class="fw-medium text-muted">تأیید شده</span>
                    <h3 class="mb-0 mt-1">{{ $stats['approved'] }}</h3>
                </div>
                <span class="badge bg-label-success rounded p-2"><i class="bx bx-check-circle bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>
