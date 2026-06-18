<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">کل واحدها</span>
                    <h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3>
                </div>
                <span class="badge bg-label-primary rounded p-2"><i class="bx bx-ruler bx-sm"></i></span>
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
                <span class="badge bg-label-success rounded p-2"><i class="bx bx-check-circle bx-sm"></i></span>
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
                <span class="badge bg-label-danger rounded p-2"><i class="bx bx-x-circle bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-xl-3">
    <div class="card shadow-none border">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
                <div class="content-left">
                    <span class="fw-medium text-muted">دارای والد</span>
                    <h3 class="mb-0 mt-1">-</h3>
                </div>
                <span class="badge bg-label-info rounded p-2"><i class="bx bx-git-branch bx-sm"></i></span>
            </div>
        </div>
    </div>
</div>