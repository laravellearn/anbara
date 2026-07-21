@extends('layouts.master')
@section('title', 'سفارش خرید — ' . $po->po_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- نوار عنوان + دکمه‌های عملیات --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1"><i class="bx bx-cart me-2"></i> {{ $po->po_number }}</h4>
            <span class="badge bg-label-{{ $po->status_color }} fs-6">{{ $po->status_label }}</span>
            @if($po->reference_number)
            <span class="text-muted ms-2">مرجع: {{ $po->reference_number }}</span>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('warehouse.purchase-orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-list-ul me-1"></i> لیست
            </a>
            <a href="{{ route('warehouse.purchase-orders.print', $po) }}" target="_blank" class="btn btn-outline-dark btn-sm">
                <i class="bx bx-printer me-1"></i> چاپ
            </a>
            @if($po->isEditable())
                @can('access', 'purchase-orders.edit')
                <a href="{{ route('warehouse.purchase-orders.edit', $po) }}" class="btn btn-warning btn-sm">
                    <i class="bx bx-edit me-1"></i> ویرایش
                </a>
                @endcan
                @can('access', 'purchase-orders.confirm')
                <form method="POST" action="{{ route('warehouse.purchase-orders.confirm', $po) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-check-circle me-1"></i> تأیید</button>
                </form>
                @endcan
            @endif
            @if($po->canSend())
                @can('access', 'purchase-orders.confirm')
                <form method="POST" action="{{ route('warehouse.purchase-orders.mark-sent', $po) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info btn-sm text-white"><i class="bx bx-send me-1"></i> ارسال به تأمین‌کننده</button>
                </form>
                @endcan
            @endif
            @if($po->canReceive())
                @can('access', 'purchase-orders.receive')
                <a href="{{ route('warehouse.purchase-orders.receive-form', $po) }}" class="btn btn-success btn-sm">
                    <i class="bx bx-import me-1"></i> ثبت دریافت
                </a>
                @endcan
            @endif
            @if($po->canClose())
                @can('access', 'purchase-orders.confirm')
                <form method="POST" action="{{ route('warehouse.purchase-orders.close', $po) }}" class="d-inline"
                      onsubmit="return confirm('سفارش بسته شود؟')">
                    @csrf
                    <button type="submit" class="btn btn-dark btn-sm"><i class="bx bx-lock me-1"></i> بستن PO</button>
                </form>
                @endcan
            @endif
            @if($po->canCancel())
                @can('access', 'purchase-orders.confirm')
                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="bx bx-x-circle me-1"></i> لغو
                </button>
                @endcan
            @endif
        </div>
    </div>

    @if(session('toast'))
    <div class="alert alert-{{ session('toast.type') }} alert-dismissible fade show">
        {{ session('toast.message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="row g-4">
        {{-- اطلاعات سفارش --}}
        <div class="col-lg-8">
            <div class="card shadow-none border mb-4">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">اطلاعات کلی</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">تأمین‌کننده</label>
                            <div class="fw-medium">{{ $po->supplier?->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">انبار دریافت</label>
                            <div class="fw-medium">{{ $po->warehouse?->title }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">تاریخ سفارش</label>
                            <div>{{ $po->order_date?->format('Y/m/d') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">تحویل پیش‌بینی</label>
                            <div>{{ $po->expected_delivery_date?->format('Y/m/d') ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">تحویل واقعی</label>
                            <div>{{ $po->actual_delivery_date?->format('Y/m/d') ?? '—' }}</div>
                        </div>
                        @if($po->notes)
                        <div class="col-12">
                            <label class="form-label text-muted small">یادداشت</label>
                            <div>{{ $po->notes }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ردیف‌های کالا --}}
            <div class="card shadow-none border mb-4">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">اقلام سفارش</h6>
                    <span class="badge bg-label-primary">{{ $po->receipt_percent }}% دریافت شده</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>کالا</th>
                                <th>واحد</th>
                                <th class="text-end">سفارش</th>
                                <th class="text-end">دریافت</th>
                                <th class="text-end">مانده</th>
                                <th class="text-end">قیمت واحد</th>
                                <th class="text-end">جمع ردیف</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($po->items as $item)
                            <tr>
                                <td class="fw-medium">{{ $item->product?->title }}</td>
                                <td><small>{{ $item->measurementUnit?->title ?? '—' }}</small></td>
                                <td class="text-end">{{ number_format($item->quantity_ordered, 2) }}</td>
                                <td class="text-end text-success">{{ number_format($item->quantity_received, 2) }}</td>
                                <td class="text-end {{ $item->remaining_qty > 0 ? 'text-warning' : 'text-success' }}">
                                    {{ number_format($item->remaining_qty, 2) }}
                                </td>
                                <td class="text-end">{{ $item->unit_price ? number_format($item->unit_price) . ' ﷼' : '—' }}</td>
                                <td class="text-end fw-medium">{{ $item->unit_price ? number_format($item->line_total) . ' ﷼' : '—' }}</td>
                                <td>
                                    @if($item->isFullyReceived())
                                        <span class="badge bg-label-success">دریافت کامل</span>
                                    @elseif($item->quantity_received > 0)
                                        <span class="badge bg-label-warning">جزئی</span>
                                    @else
                                        <span class="badge bg-label-secondary">دریافت‌نشده</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ستون راست --}}
        <div class="col-lg-4">
            {{-- خلاصه مالی --}}
            <div class="card shadow-none border mb-4">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">خلاصه مالی</h6></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">جمع کالا</span>
                        <strong>{{ number_format($po->subtotal) }} ﷼</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">تخفیف ({{ $po->discount_percent }}%)</span>
                        <strong class="text-danger">{{ number_format($po->discount_amount) }} ﷼</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">مالیات ({{ $po->tax_percent }}%)</span>
                        <strong>{{ number_format($po->tax_amount) }} ﷼</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">حمل</span>
                        <strong>{{ number_format($po->shipping_cost) }} ﷼</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">جمع نهایی</span>
                        <strong class="text-primary fs-5">{{ number_format($po->total_amount) }} ﷼</strong>
                    </div>
                </div>
            </div>

            {{-- تاریخچه --}}
            <div class="card shadow-none border">
                <div class="card-header border-bottom"><h6 class="card-title mb-0">تاریخچه</h6></div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <span class="text-muted small">ثبت توسط {{ $po->creator?->name ?? '—' }}</span><br>
                            <small class="text-muted">{{ $po->created_at?->format('Y/m/d H:i') }}</small>
                        </div>
                        @if($po->confirmed_at)
                        <div class="timeline-item mb-3">
                            <span class="text-primary small">تأیید توسط {{ $po->confirmer?->name ?? '—' }}</span><br>
                            <small class="text-muted">{{ $po->confirmed_at?->format('Y/m/d H:i') }}</small>
                        </div>
                        @endif
                        @if($po->sent_at)
                        <div class="timeline-item mb-3">
                            <span class="text-info small">ارسال به تأمین‌کننده</span><br>
                            <small class="text-muted">{{ $po->sent_at?->format('Y/m/d H:i') }}</small>
                        </div>
                        @endif
                        @if($po->closed_at)
                        <div class="timeline-item">
                            <span class="text-dark small">بسته شده</span><br>
                            <small class="text-muted">{{ $po->closed_at?->format('Y/m/d H:i') }}</small>
                        </div>
                        @endif
                        @if($po->cancellation_reason)
                        <div class="timeline-item mt-2">
                            <span class="text-danger small">لغو: {{ $po->cancellation_reason }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal لغو --}}
@if($po->canCancel())
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('warehouse.purchase-orders.cancel', $po) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bx bx-x-circle me-1"></i> لغو سفارش</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">دلیل لغو (اختیاری)</label>
                    <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="دلیل لغو..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-danger">تأیید لغو</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
