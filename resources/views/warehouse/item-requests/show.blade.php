@extends('layouts.master')
@section('title', 'درخواست کالا ' . $itemRequest->ir_number)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bx bx-task me-2 text-primary"></i> {{ $itemRequest->ir_number }}</h4>
            <span class="badge bg-label-{{ $itemRequest->status_color }} fs-6">{{ $itemRequest->status_label }}</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($itemRequest->canSubmit())
            <form method="POST" action="{{ route('warehouse.item-requests.submit', $itemRequest) }}" class="d-inline">
                @csrf <button class="btn btn-info btn-sm"><i class="bx bx-send me-1"></i> ارسال برای بررسی</button>
            </form>
            @endif
            @if($itemRequest->canApprove())
            <form method="POST" action="{{ route('warehouse.item-requests.approve', $itemRequest) }}" class="d-inline">
                @csrf <button class="btn btn-success btn-sm"><i class="bx bx-check me-1"></i> تأیید</button>
            </form>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bx bx-x me-1"></i> رد</button>
            @endif
            @if($itemRequest->canIssue())
            <form method="POST" action="{{ route('warehouse.item-requests.issue', $itemRequest) }}" class="d-inline"
                  onsubmit="return confirm('آیا از صدور حواله انبار برای این درخواست مطمئن هستید؟')">
                @csrf <button class="btn btn-primary btn-sm"><i class="bx bx-export me-1"></i> صدور حواله انبار</button>
            </form>
            @endif
            @if($itemRequest->isEditable())
            <a href="{{ route('warehouse.item-requests.edit', $itemRequest) }}" class="btn btn-warning btn-sm"><i class="bx bx-edit me-1"></i> ویرایش</a>
            @endif
            <a href="{{ route('warehouse.item-requests.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> بازگشت</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-none border mb-4">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">اطلاعات درخواست</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><span class="text-muted d-block small">شماره IR</span><strong>{{ $itemRequest->ir_number }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">تاریخ درخواست</span><strong>{{ $itemRequest->request_date->format('Y-m-d') }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">مورد نیاز تا</span><strong>{{ $itemRequest->required_by_date?->format('Y-m-d') ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">درخواست‌دهنده</span><strong>{{ $itemRequest->requester?->name }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">انبار</span><strong>{{ $itemRequest->warehouse?->title }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">واحد سازمانی</span><strong>{{ $itemRequest->organizationalUnit?->title ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">مرکز هزینه</span><strong>{{ $itemRequest->costCenter?->title ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">اولویت</span>
                            <span class="badge bg-label-{{ \App\Models\PurchaseRequest::priorityColors()[$itemRequest->priority] ?? 'secondary' }}">{{ \App\Models\PurchaseRequest::priorityLabels()[$itemRequest->priority] ?? $itemRequest->priority }}</span>
                        </div>
                        @if($itemRequest->purpose)
                        <div class="col-12"><span class="text-muted d-block small">هدف درخواست</span><p class="mb-0">{{ $itemRequest->purpose }}</p></div>
                        @endif
                        @if($itemRequest->notes)
                        <div class="col-12"><span class="text-muted d-block small">یادداشت</span><p class="mb-0">{{ $itemRequest->notes }}</p></div>
                        @endif
                        @if($itemRequest->rejection_reason)
                        <div class="col-12"><span class="text-muted d-block small text-danger">دلیل رد</span><p class="mb-0 text-danger">{{ $itemRequest->rejection_reason }}</p></div>
                        @endif
                        @if($itemRequest->warehouseDocument)
                        <div class="col-12">
                            <span class="text-muted d-block small">حواله انبار صادر شده</span>
                            <a href="{{ route('warehouse.documents.show', $itemRequest->warehouseDocument) }}" class="fw-medium text-primary">{{ $itemRequest->warehouseDocument->document_number }}</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-none border">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">اقلام درخواست</h6></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>کالا</th><th>واحد</th><th>مقدار درخواستی</th><th>مقدار تحویل‌شده</th><th>توضیح</th></tr>
                        </thead>
                        <tbody>
                            @foreach($itemRequest->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->product?->title }}</td>
                                <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                                <td>{{ number_format($item->quantity_requested, 2) }}</td>
                                <td>
                                    @if($item->quantity_issued > 0)
                                    <span class="text-success fw-medium">{{ number_format($item->quantity_issued, 2) }}</span>
                                    @else <span class="text-muted">—</span> @endif
                                </td>
                                <td>{{ $item->description ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-none border">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">تاریخچه</h6></div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-primary"><i class="bx bx-plus-circle"></i></span>
                            <div class="timeline-event"><div class="fw-medium">ایجاد درخواست</div><small class="text-muted">{{ $itemRequest->created_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @if($itemRequest->submitted_at)
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-info"><i class="bx bx-send"></i></span>
                            <div class="timeline-event"><div class="fw-medium">ارسال برای بررسی</div><small class="text-muted">{{ $itemRequest->submitted_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @endif
                        @if($itemRequest->approved_at)
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-success"><i class="bx bx-check"></i></span>
                            <div class="timeline-event"><div class="fw-medium">تأیید توسط {{ $itemRequest->approver?->name }}</div><small class="text-muted">{{ $itemRequest->approved_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @endif
                        @if($itemRequest->rejected_at)
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-danger"><i class="bx bx-x"></i></span>
                            <div class="timeline-event"><div class="fw-medium">رد توسط {{ $itemRequest->approver?->name }}</div><small class="text-muted">{{ $itemRequest->rejected_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @endif
                        @if($itemRequest->issued_at)
                        <li class="timeline-item">
                            <span class="timeline-indicator bg-primary"><i class="bx bx-export"></i></span>
                            <div class="timeline-event"><div class="fw-medium">صدور حواله انبار</div><small class="text-muted">{{ $itemRequest->issued_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- مودال رد کردن --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">رد درخواست کالا</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('warehouse.item-requests.reject', $itemRequest) }}">
            @csrf
            <div class="modal-body">
                <label class="form-label fw-medium">دلیل رد <span class="text-danger">*</span></label>
                <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="دلیل رد درخواست..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-danger">رد درخواست</button>
            </div>
        </form>
    </div></div>
</div>
@endsection
