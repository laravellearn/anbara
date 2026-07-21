@extends('layouts.master')
@section('title', 'فاکتور خرید ' . $purchaseInvoice->invoice_number)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bx bx-receipt me-2 text-primary"></i> {{ $purchaseInvoice->invoice_number }}</h4>
            <span class="badge bg-label-{{ $purchaseInvoice->status_color }} fs-6">{{ $purchaseInvoice->status_label }}</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @if($purchaseInvoice->canRegister())
            <form method="POST" action="{{ route('warehouse.purchase-invoices.register', $purchaseInvoice) }}" class="d-inline">
                @csrf <button class="btn btn-info btn-sm"><i class="bx bx-check me-1"></i> ثبت رسمی</button>
            </form>
            @endif
            @if($purchaseInvoice->canPay())
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#payModal"><i class="bx bx-money me-1"></i> ثبت پرداخت</button>
            @endif
            @if($purchaseInvoice->canCancel())
            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal"><i class="bx bx-x me-1"></i> لغو</button>
            @endif
            @if($purchaseInvoice->isEditable())
            <a href="{{ route('warehouse.purchase-invoices.edit', $purchaseInvoice) }}" class="btn btn-warning btn-sm"><i class="bx bx-edit me-1"></i> ویرایش</a>
            @endif
            <a href="{{ route('warehouse.purchase-invoices.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bx bx-arrow-back me-1"></i> بازگشت</a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-none border mb-4">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">اطلاعات فاکتور</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><span class="text-muted d-block small">شماره داخلی</span><strong>{{ $purchaseInvoice->invoice_number }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">شماره تأمین‌کننده</span><strong>{{ $purchaseInvoice->supplier_invoice_number ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">تاریخ فاکتور</span><strong>{{ $purchaseInvoice->invoice_date->format('Y-m-d') }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">تأمین‌کننده</span><strong>{{ $purchaseInvoice->supplier?->name ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">سفارش مرتبط</span>
                            @if($purchaseInvoice->purchaseOrder)
                            <a href="{{ route('warehouse.purchase-orders.show', $purchaseInvoice->purchaseOrder) }}" class="fw-medium text-primary">{{ $purchaseInvoice->purchaseOrder->po_number }}</a>
                            @else <strong>—</strong> @endif
                        </div>
                        <div class="col-md-4"><span class="text-muted d-block small">سررسید</span><strong>{{ $purchaseInvoice->due_date?->format('Y-m-d') ?? '—' }}</strong></div>
                        @if($purchaseInvoice->payment_date)
                        <div class="col-md-4"><span class="text-muted d-block small">تاریخ پرداخت</span><strong>{{ $purchaseInvoice->payment_date->format('Y-m-d') }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">روش پرداخت</span><strong>{{ $purchaseInvoice->payment_method ?? '—' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block small">مرجع پرداخت</span><strong>{{ $purchaseInvoice->payment_reference ?? '—' }}</strong></div>
                        @endif
                        @if($purchaseInvoice->notes)
                        <div class="col-12"><span class="text-muted d-block small">یادداشت</span><p class="mb-0">{{ $purchaseInvoice->notes }}</p></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-none border">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">اقلام فاکتور</h6></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>#</th><th>کالا</th><th>واحد</th><th>تعداد</th><th>قیمت واحد</th><th>تخفیف %</th><th>جمع ردیف</th></tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseInvoice->items as $i => $item)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $item->product?->title }}</td>
                                <td>{{ $item->measurementUnit?->title ?? '—' }}</td>
                                <td>{{ number_format($item->quantity, 2) }}</td>
                                <td>{{ number_format($item->unit_price) }}</td>
                                <td>{{ $item->discount_percent }}%</td>
                                <td class="fw-medium">{{ number_format($item->line_total) }} ریال</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr><td colspan="6" class="text-end fw-medium">جمع اقلام:</td><td>{{ number_format($purchaseInvoice->subtotal) }} ریال</td></tr>
                            <tr><td colspan="6" class="text-end text-danger">تخفیف ({{ $purchaseInvoice->discount_percent }}%):</td><td class="text-danger">{{ number_format($purchaseInvoice->discount_amount) }} ریال</td></tr>
                            <tr><td colspan="6" class="text-end">مالیات ({{ $purchaseInvoice->tax_percent }}%):</td><td>{{ number_format($purchaseInvoice->tax_amount) }} ریال</td></tr>
                            <tr><td colspan="6" class="text-end">هزینه حمل:</td><td>{{ number_format($purchaseInvoice->shipping_cost) }} ریال</td></tr>
                            <tr class="fw-bold fs-6"><td colspan="6" class="text-end text-primary">مبلغ کل:</td><td class="text-primary">{{ number_format($purchaseInvoice->total_amount) }} ریال</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-none border">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">وضعیت ثبت</h6></div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-primary"><i class="bx bx-plus-circle"></i></span>
                            <div class="timeline-event"><div class="fw-medium">ایجاد فاکتور</div><small class="text-muted">{{ $purchaseInvoice->created_at->format('Y-m-d H:i') }} — {{ $purchaseInvoice->creator?->name }}</small></div>
                        </li>
                        @if($purchaseInvoice->registered_at)
                        <li class="timeline-item pb-3">
                            <span class="timeline-indicator bg-info"><i class="bx bx-check"></i></span>
                            <div class="timeline-event"><div class="fw-medium">ثبت رسمی</div><small class="text-muted">{{ $purchaseInvoice->registered_at->format('Y-m-d H:i') }} — {{ $purchaseInvoice->registeredBy?->name }}</small></div>
                        </li>
                        @endif
                        @if($purchaseInvoice->payment_date)
                        <li class="timeline-item">
                            <span class="timeline-indicator bg-success"><i class="bx bx-money"></i></span>
                            <div class="timeline-event"><div class="fw-medium">پرداخت شده</div><small class="text-muted">{{ $purchaseInvoice->payment_date->format('Y-m-d') }}</small></div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- مودال پرداخت --}}
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">ثبت پرداخت</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('warehouse.purchase-invoices.mark-paid', $purchaseInvoice) }}">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-medium">روش پرداخت <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash">نقدی</option>
                        <option value="cheque">چک</option>
                        <option value="bank_transfer">انتقال بانکی</option>
                        <option value="credit">اعتباری</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">شماره مرجع / چک</label>
                    <input type="text" name="payment_reference" class="form-control" placeholder="اختیاری">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-medium">تاریخ پرداخت <span class="text-danger">*</span></label>
                    <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-success"><i class="bx bx-money me-1"></i> ثبت پرداخت</button>
            </div>
        </form>
    </div></div>
</div>

{{-- مودال لغو --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">لغو فاکتور</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('warehouse.purchase-invoices.cancel', $purchaseInvoice) }}">
            @csrf
            <div class="modal-body">
                <label class="form-label fw-medium">دلیل لغو <span class="text-danger">*</span></label>
                <textarea name="cancellation_reason" class="form-control" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-danger">لغو فاکتور</button>
            </div>
        </form>
    </div></div>
</div>
@endsection
