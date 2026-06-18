<div class="col-sm-6 col-xl-4">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">کل مخاطبین</span>
                    <h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <span class="badge bg-label-primary rounded p-2"><i class="bx bx-user-pin bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-4">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">مشتری</span>
                    <h3 class="mb-0 mt-1">{{ $stats['customer'] }}</h3>
                </div>
                <span class="badge bg-label-success rounded p-2"><i class="bx bx-cart bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-4">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">تأمین‌کننده</span>
                    <h3 class="mb-0 mt-1">{{ $stats['supplier'] }}</h3>
                </div>
                <span class="badge bg-label-danger rounded p-2"><i class="bx bx-package bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>