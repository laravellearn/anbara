@extends('layouts.master')
@section('title', 'درخواست خرید ' . $purchaseRequest->pr_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- هدر --}}
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bx bx-cart-add me-2 text-primary"></i> {{ $purchaseRequest->pr_number }}</h4>
            <span class="badge bg-label-{{ $purchaseRequest->status_color }} fs-6">{{ $purchaseRequest->status_label }}</span>
            <span class="badge bg-label-{{ $purchaseRequest->priority_color }} ms-1">{{ $purchaseRequest->priority_label }}</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            {{-- دکمه‌های عملیات --}}
            @if($purchaseRequest->canSubmit())
            <form method="POST" action="{{ route('warehouse.purchase-requests.submit', $purchaseRequest) }}" class="d-inline">
                @csrf
                <button class="btn btn-info btn-sm"><i class="bx bx-send me-1"></i> ارسال برای بررسی</button>
            </form>
            @endif

            @if($purchaseRequest->canApprove())
            <form method="POST" action="{{ route('warehouse.purchase-requests.approve', $purchaseRequest) }}" class="d-inline">
                @csrf
                <button class="btn btn-success btn-sm"><i class="bx bx-check me-1"></i> تأیید</button>
            </form>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bx bx-x me-1"></i> رد</button>
            @endif

            @if($purchaseRequest->canConvert())
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#convertModal"><i class="bx bx-transfer me-1"></i> تبدیل به سفارش خرید</button>
            @endif

            @if($purchaseRequest->isEditable())
            <a href="{{ route('warehouse.purchase-requests.edit', $purchaseRequest) }}" class="btn btn-warning btn-sm"><i class="bx bx-edit me-1"></i> ویرایش</a>
            @endif

            <a href="{{ route('warehouse.purchase-requests.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> بازگشت</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        {{-- اطلاعات اصلی --}}
        <div class="col-lg-8">
            <div class="card shadow-none border mb-4">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">اطلاعات درخواست</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><span class="text-muted d-block small">شماره PR</span><strong>{{ $purchaseRequest->pr_number }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">تاریخ درخواست</span><strong>{{ $purchaseRequest->request_date->format('Y-m-d') }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">مورد نیاز تا</span><strong>{{ $purchaseRequest->required_by_date?->format('Y-m-d') ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">درخواست‌دهنده</span><strong>{{ $purchaseRequest->requester?->name }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">انبار</span><strong>{{ $purchaseRequest->warehouse?->title ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">مرکز هزینه</span><strong>{{ $purchaseRequest->costCenter?->title ?? '—' }}</strong></div>
                        @if($purchaseRequest->reason)
                        <div class="col-12"><span class="text-muted d-block small">دلیل درخواست</span><p class="mb-0">{{ $purchaseRequest->reason }}</p></div>
                        @endif
                        @if($purchaseRequest->notes)
                        <div class="col-12"><span class="text-muted d-block small">یادداشت</span><p class="mb-0">{{ $purchaseRequest->notes }}</p></div>
                        @endif
                        @if($purchaseRequest->rejection_reason)
                        <div class="col-12"><span class="text-muted d-block small text-danger">دلیل رد</span><p class="mb-0 text-danger">{{ $purchaseRequest->rejection_reason }}</p></div>
                        @endif
                        @if($purchaseRequest->purchaseOrder)
                        <div class="col-12">
                            <span class="text-muted d-block small">سفارش خرید مرتبط</span>
                            <a href="{{ route('warehouse.purchase-orders.show', $purchaseRequest->purchaseOrder) }}" class="fw-medium text-primary">{{ $purchaseRequest->purchaseOrder->po_number }}</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- اقلام --}}
            <div class="card shadow-none border">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">اقلام درخواست</h6></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>کالا</th><th>واحد</th><th>مقدار</th><th>قیمت تخمینی</th><th>جمع</th><th>توضیح</th></tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseRequest->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->product?->title }}</td>
                                <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                                <td>{{ number_format($item->quantity_requested, 2) }}</td>
                                <td>{{ $item->estimated_unit_price ? number_format($item->estimated_unit_price) : '—' }}</td>
                                <td>{{ $item->estimated_unit_price ? number_format($item->line_total) : '—' }}</td>
                                <td>{{ $item->description ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-medium">برآورد کل:</td>
                                <td class="fw-bold text-primary">{{ number_format($purchaseRequest->total_estimated) }} ریال</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- تایم‌لاین --}}
        <div class="col-lg-4">
            <div class="card shadow-none border">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">تاریخچه</h6></div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-primary"><i class="bx bx-plus-circle"></i></span>
                            <div class="timeline-event"><div class="fw-medium">ایجاد درخواست</div><small class="text-muted">{{ $purchaseRequest->created_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @if($purchaseRequest->submitted_at)
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-info"><i class="bx bx-send"></i></span>
                            <div class="timeline-event"><div class="fw-medium">ارسال برای بررسی</div><small class="text-muted">{{ $purchaseRequest->submitted_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @endif
                        @if($purchaseRequest->approved_at)
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-success"><i class="bx bx-check"></i></span>
                            <div class="timeline-event"><div class="fw-medium">تأیید توسط {{ $purchaseRequest->approver?->name }}</div><small class="text-muted">{{ $purchaseRequest->approved_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @endif
                        @if($purchaseRequest->rejected_at)
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-danger"><i class="bx bx-x"></i></span>
                            <div class="timeline-event"><div class="fw-medium">رد توسط {{ $purchaseRequest->approver?->name }}</div><small class="text-muted">{{ $purchaseRequest->rejected_at->format('Y-m-d H:i') }}</small></div>
                        </li>
                        @endif
                        @if($purchaseRequest->converted_at)
                        <li class="timeline-item">
                            <span class="timeline-indicator bg-primary"><i class="bx bx-transfer"></i></span>
                            <div class="timeline-event"><div class="fw-medium">تبدیل به سفارش خرید</div><small class="text-muted">{{ $purchaseRequest->converted_at->format('Y-m-d H:i') }}</small></div>
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
        <div class="modal-header"><h5 class="modal-title">رد درخواست</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('warehouse.purchase-requests.reject', $purchaseRequest) }}">
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

{{-- مودال تبدیل به PO --}}
<div class="modal fade" id="convertModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">تبدیل به سفارش خرید</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('warehouse.purchase-requests.convert', $purchaseRequest) }}">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-medium">تاریخ سفارش <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">انبار دریافت <span class="text-danger">*</span></label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">انتخاب انبار...</option>
                        @foreach(\App\Models\Warehouse::where('tenant_id', auth()->user()->tenant_id)->where('is_active', true)->get() as $wh)
                        <option value="{{ $wh->id }}" @selected($purchaseRequest->warehouse_id == $wh->id)>{{ $wh->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">تأمین‌کننده</label>
                    <select name="supplier_id" class="form-select">
                        <option value="">انتخاب...</option>
                        @foreach(\App\Models\Contact::where('tenant_id', auth()->user()->tenant_id)->get() as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-transfer me-1"></i> ایجاد سفارش خرید</button>
            </div>
        </form>
    </div></div>
</div>
@endsection
