@extends('layouts.master')
@section('title', 'سند انبار — ' . $document->document_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- هدر سند --}}
    <div class="card shadow-none border mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h5 class="card-title mb-0">
                    <i class="bx bx-file me-1"></i> {{ $document->document_number }}
                </h5>
                <span class="badge bg-label-{{ $document->type_color }} fs-6">{{ $document->type_label }}</span>
                <span class="badge bg-label-{{ $document->status_color }}">{{ $document->status_label }}</span>
            </div>

            {{-- دکمه‌های گردش‌کار --}}
            <div class="d-flex gap-2 flex-wrap">
                @can('access', 'warehouse-documents.submit')
                @if($document->status === 'draft')
                <form action="{{ route('warehouse.documents.submit', $document) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-info"><i class="bx bx-send me-1"></i> ارسال برای تأیید</button>
                </form>
                @endif
                @endcan

                @can('access', 'warehouse-documents.approve')
                @if($document->isPending())
                <form action="{{ route('warehouse.documents.approve', $document) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-success"><i class="bx bx-check me-1"></i> تأیید</button>
                </form>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bx bx-x me-1"></i> رد
                </button>
                @endif
                @if($document->isApproved())
                <form action="{{ route('warehouse.documents.cancel', $document) }}" method="POST" class="d-inline cancel-form">
                    @csrf
                    <button class="btn btn-sm btn-outline-warning"><i class="bx bx-undo me-1"></i> لغو سند</button>
                </form>
                @endif
                @endcan

                @can('access', 'warehouse-documents.edit')
                @if($document->isEditable())
                <a href="{{ route('warehouse.documents.edit', $document) }}" class="btn btn-sm btn-warning">
                    <i class="bx bx-edit me-1"></i> ویرایش
                </a>
                @endif
                @endcan

                <a href="{{ route('warehouse.documents.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i> بازگشت
                </a>
                <a href="{{ route('warehouse.documents.print', $document) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                    <i class="bx bx-printer me-1"></i> چاپ
                </a>
            </div>
        </div>

        {{-- اطلاعات سند --}}
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><th class="text-muted" width="40%">انبار</th>
                            <td>{{ $document->warehouse->title ?? '—' }}
                                @if($document->type === 'transfer' && $document->destinationWarehouse)
                                <i class="bx bx-right-arrow-alt mx-1 text-muted"></i>
                                {{ $document->destinationWarehouse->title }}
                                @endif
                            </td></tr>
                        <tr><th class="text-muted">تاریخ سند</th><td>{{ $document->document_date?->format('Y/m/d') }}</td></tr>
                        <tr><th class="text-muted">شماره مرجع</th><td>{{ $document->reference_number ?? '—' }}</td></tr>
                        <tr><th class="text-muted">طرف حساب</th><td>{{ $document->contact->name ?? '—' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                        <tr><th class="text-muted" width="40%">سال مالی</th><td>{{ $document->fiscalYear->name ?? '—' }}</td></tr>
                        <tr><th class="text-muted">مرکز هزینه</th><td>{{ $document->costCenter->title ?? '—' }}</td></tr>
                        <tr><th class="text-muted">ثبت‌کننده</th><td>{{ $document->creator->name ?? '—' }}</td></tr>
                        @if($document->approver)
                        <tr><th class="text-muted">تأییدکننده</th><td>{{ $document->approver->name }} — {{ $document->approved_at?->format('Y/m/d H:i') }}</td></tr>
                        @endif
                        @if($document->rejection_reason)
                        <tr><th class="text-muted">دلیل رد</th><td class="text-danger">{{ $document->rejection_reason }}</td></tr>
                        @endif
                    </table>
                </div>
                @if($document->description)
                <div class="col-12">
                    <div class="alert alert-light border py-2 mb-0"><strong>توضیحات:</strong> {{ $document->description }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- اقلام سند --}}
    <div class="card shadow-none border">
        <div class="card-header border-bottom">
            <h6 class="card-title mb-0"><i class="bx bx-list-ul me-1"></i> اقلام سند ({{ $document->items->count() }} قلم)</h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>کالا</th>
                        <th>واحد</th>
                        <th class="text-end">مقدار</th>
                        <th class="text-end">قیمت واحد</th>
                        <th class="text-end">ارزش کل</th>
                        <th>سریال / بچ</th>
                        <th>انقضا</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($document->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-medium">{{ $item->product->title ?? '—' }}</div>
                            <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                        </td>
                        <td><small>{{ $item->measurementUnit->title ?? '—' }}</small></td>
                        <td class="text-end fw-medium">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-end">{{ $item->unit_price ? number_format($item->unit_price) . ' ﷼' : '—' }}</td>
                        <td class="text-end">{{ $item->unit_price ? number_format($item->total_value) . ' ﷼' : '—' }}</td>
                        <td><small class="text-muted">{{ $item->serial_number ?? '' }}{{ $item->batch_number ? ' / '.$item->batch_number : '' }}</small></td>
                        <td><small>{{ $item->expiry_date?->format('Y/m/d') ?? '—' }}</small></td>
                    </tr>
                    @endforeach
                </tbody>
                @if($document->items->where('unit_price', '!=', null)->count())
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold">جمع کل ارزش:</td>
                        <td class="text-end fw-bold text-primary">
                            {{ number_format($document->items->sum('total_value')) }} ﷼
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- مودال رد سند --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('warehouse.documents.reject', $document) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="bx bx-x-circle me-1"></i> رد سند</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">دلیل رد (اختیاری)</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="توضیح دهید چرا این سند رد می‌شود..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-danger"><i class="bx bx-x me-1"></i> رد سند</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- پیوست‌ها --}}
@include('components.attachments-panel', ['model' => $doc, 'modelType' => 'WarehouseDocument'])
@endsection

@push('scripts')
<script>
$(function () {
    $('.cancel-form').on('submit', function (e) {
        e.preventDefault();
        if (confirm('آیا از لغو این سند و معکوس شدن موجودی مطمئن هستید؟')) this.submit();
    });
});
</script>
@endpush
