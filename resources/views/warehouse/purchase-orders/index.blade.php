@extends('layouts.master')
@section('title', 'سفارشات خرید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- KPI --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['label'=>'کل سفارشات','value'=>$stats['total'],'color'=>'primary','icon'=>'bx-cart'],
            ['label'=>'پیش‌نویس','value'=>$stats['draft'],'color'=>'secondary','icon'=>'bx-edit'],
            ['label'=>'در انتظار / ارسال','value'=>$stats['pending'],'color'=>'info','icon'=>'bx-send'],
            ['label'=>'باز (دریافت‌نشده)','value'=>$stats['open'],'color'=>'warning','icon'=>'bx-time'],
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
            <form method="GET" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-medium">جستجو</label>
                        <input type="text" name="search" class="form-control" placeholder="شماره PO یا مرجع..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">وضعیت</label>
                        <select name="status" class="form-select">
                            <option value="">همه</option>
                            @foreach(\App\Models\PurchaseOrder::statusLabels() as $k => $v)
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
                        <label class="form-label fw-medium">تا تاریخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> جستجو</button>
                        <a href="{{ route('warehouse.purchase-orders.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-cart me-1"></i> سفارشات خرید</h5>
            @can('access', 'purchase-orders.create')
            <a href="{{ route('warehouse.purchase-orders.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i> سفارش جدید
            </a>
            @endcan
        </div>
        @include('warehouse.purchase-orders._table', ['orders' => $orders])
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $orders->firstItem() ?? 0 }} تا {{ $orders->lastItem() ?? 0 }} از {{ $orders->total() }}</small>
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
