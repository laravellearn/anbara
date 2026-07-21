@extends('layouts.master')
@section('title', 'فاکتورهای خرید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- KPIs --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['label'=>'کل فاکتورها','value'=>$stats['total'],'color'=>'primary','icon'=>'bx-receipt'],
            ['label'=>'پیش‌نویس','value'=>$stats['draft'],'color'=>'secondary','icon'=>'bx-edit'],
            ['label'=>'ثبت شده','value'=>$stats['registered'],'color'=>'info','icon'=>'bx-check-circle'],
            ['label'=>'پرداخت‌نشده','value'=>$stats['unpaid'],'color'=>'warning','icon'=>'bx-time'],
        ] as $card)
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div><span class="fw-medium text-muted">{{ $card['label'] }}</span><h3 class="mb-0 mt-1">{{ $card['value'] }}</h3></div>
                    <span class="badge bg-label-{{ $card['color'] }} rounded p-2"><i class="bx {{ $card['icon'] }} bx-sm"></i></span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- فیلتر --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">جستجو</label>
                        <input type="text" name="search" class="form-control" placeholder="شماره فاکتور..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">وضعیت</label>
                        <select name="status" class="form-select">
                            <option value="">همه</option>
                            @foreach(\App\Models\PurchaseInvoice::statusLabels() as $k => $v)
                            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">تأمین‌کننده</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">همه</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">از تاریخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-medium">تا</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> جستجو</button>
                        <a href="{{ route('warehouse.purchase-invoices.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-receipt me-1"></i> فاکتورهای خرید</h5>
            @can('access', 'purchase-invoices.create')
            <a href="{{ route('warehouse.purchase-invoices.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i> فاکتور جدید
            </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>شماره فاکتور</th>
                        <th>ش. فاکتور تأمین‌کننده</th>
                        <th>تاریخ</th>
                        <th>تأمین‌کننده</th>
                        <th>سفارش مرتبط</th>
                        <th>وضعیت</th>
                        <th>سررسید</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td><a href="{{ route('warehouse.purchase-invoices.show', $inv) }}" class="fw-medium text-primary">{{ $inv->invoice_number }}</a></td>
                        <td>{{ $inv->supplier_invoice_number ?? '—' }}</td>
                        <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                        <td>{{ $inv->supplier?->name ?? '—' }}</td>
                        <td>
                            @if($inv->purchaseOrder)
                            <a href="{{ route('warehouse.purchase-orders.show', $inv->purchaseOrder) }}" class="text-muted small">{{ $inv->purchaseOrder->po_number }}</a>
                            @else —
                            @endif
                        </td>
                        <td><span class="badge bg-label-{{ $inv->status_color }}">{{ $inv->status_label }}</span></td>
                        <td>{{ $inv->due_date?->format('Y-m-d') ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('warehouse.purchase-invoices.show', $inv) }}" class="btn btn-sm btn-icon btn-outline-primary" title="مشاهده"><i class="bx bx-show"></i></a>
                                @if($inv->isEditable())
                                <a href="{{ route('warehouse.purchase-invoices.edit', $inv) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش"><i class="bx bx-edit"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">هیچ فاکتوری ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $invoices->firstItem() ?? 0 }} تا {{ $invoices->lastItem() ?? 0 }} از {{ $invoices->total() }}</small>
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
