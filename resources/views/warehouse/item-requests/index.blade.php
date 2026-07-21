@extends('layouts.master')
@section('title', 'درخواست‌های کالا از انبار')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- KPIs --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['label'=>'کل درخواست‌ها','value'=>$stats['total'],'color'=>'primary','icon'=>'bx-task'],
            ['label'=>'پیش‌نویس','value'=>$stats['draft'],'color'=>'secondary','icon'=>'bx-edit'],
            ['label'=>'در انتظار بررسی','value'=>$stats['submitted'],'color'=>'info','icon'=>'bx-time'],
            ['label'=>'تأیید شده','value'=>$stats['approved'],'color'=>'success','icon'=>'bx-check-circle'],
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
                        <input type="text" name="search" class="form-control" placeholder="شماره IR..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">وضعیت</label>
                        <select name="status" class="form-select">
                            <option value="">همه</option>
                            @foreach(\App\Models\ItemRequest::statusLabels() as $k => $v)
                            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">انبار</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">همه</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->title }}</option>
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
                        <a href="{{ route('warehouse.item-requests.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-task me-1"></i> درخواست‌های کالا از انبار</h5>
            @can('access', 'item-requests.create')
            <a href="{{ route('warehouse.item-requests.create') }}" class="btn btn-primary btn-sm">
                <i class="bx bx-plus me-1"></i> درخواست جدید
            </a>
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>شماره IR</th>
                        <th>تاریخ</th>
                        <th>درخواست‌دهنده</th>
                        <th>انبار</th>
                        <th>واحد سازمانی</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>تعداد اقلام</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemRequests as $ir)
                    <tr>
                        <td><a href="{{ route('warehouse.item-requests.show', $ir) }}" class="fw-medium text-primary">{{ $ir->ir_number }}</a></td>
                        <td>{{ $ir->request_date->format('Y-m-d') }}</td>
                        <td>{{ $ir->requester?->name }}</td>
                        <td>{{ $ir->warehouse?->title }}</td>
                        <td>{{ $ir->organizationalUnit?->title ?? '—' }}</td>
                        <td><span class="badge bg-label-{{ \App\Models\PurchaseRequest::priorityColors()[$ir->priority] ?? 'secondary' }}">{{ \App\Models\PurchaseRequest::priorityLabels()[$ir->priority] ?? $ir->priority }}</span></td>
                        <td><span class="badge bg-label-{{ $ir->status_color }}">{{ $ir->status_label }}</span></td>
                        <td>{{ $ir->items_count }}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('warehouse.item-requests.show', $ir) }}" class="btn btn-sm btn-icon btn-outline-primary" title="مشاهده"><i class="bx bx-show"></i></a>
                                @if($ir->isEditable())
                                <a href="{{ route('warehouse.item-requests.edit', $ir) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش"><i class="bx bx-edit"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">هیچ درخواست کالایی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $itemRequests->firstItem() ?? 0 }} تا {{ $itemRequests->lastItem() ?? 0 }} از {{ $itemRequests->total() }}</small>
            {{ $itemRequests->links() }}
        </div>
    </div>
</div>
@endsection
