@extends('layouts.master')
@section('title', 'درخواست‌های خرید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- KPIs --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['label'=>'کل درخواست‌ها','value'=>$stats['total'],'color'=>'primary','icon'=>'bx-cart-add'],
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
                        <input type="text" name="search" class="form-control" placeholder="شماره PR..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">وضعیت</label>
                        <select name="status" class="form-select">
                            <option value="">همه</option>
                            @foreach(\App\Models\PurchaseRequest::statusLabels() as $k => $v)
                            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">اولویت</label>
                        <select name="priority" class="form-select">
                            <option value="">همه</option>
                            @foreach(\App\Models\PurchaseRequest::priorityLabels() as $k => $v)
                            <option value="{{ $k }}" @selected(request('priority') === $k)>{{ $v }}</option>
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
                        <a href="{{ route('warehouse.purchase-requests.index', ['_clear_filters'=>1]) }}" class="btn btn-outline-secondary" title="پاک کردن فیلترها"><i class="bx bx-reset"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="bx bx-cart-add me-1"></i> درخواست‌های خرید</h5>
            <div class="d-flex gap-2 align-items-center">
                @can('access', 'purchase-requests.delete')
                <div id="bulk-actions" class="d-none">
                    <form method="POST" action="{{ route('warehouse.purchase-requests.bulk') }}" id="bulk-form">
                        @csrf
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('درخواست‌های انتخاب‌شده (پیش‌نویس) حذف شوند؟')">
                            <i class="bx bx-trash me-1"></i> حذف انتخاب‌شده‌ها
                        </button>
                    </form>
                </div>
                @endcan
                @can('access', 'purchase-requests.create')
                <a href="{{ route('warehouse.purchase-requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus me-1"></i> درخواست جدید
                </a>
                @endcan
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        @can('access', 'purchase-requests.delete')
                        <th style="width:36px;">
                            <input type="checkbox" class="form-check-input" id="check-all" title="انتخاب همه">
                        </th>
                        @endcan
                        <th>شماره PR</th>
                        <th>تاریخ</th>
                        <th>درخواست‌دهنده</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>تعداد اقلام</th>
                        <th>مورد نیاز تا</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $pr)
                    <tr>
                        @can('access', 'purchase-requests.delete')
                        <td><input type="checkbox" class="form-check-input row-check" name="ids[]" value="{{ $pr->id }}" form="bulk-form"></td>
                        @endcan
                        <td><a href="{{ route('warehouse.purchase-requests.show', $pr) }}" class="fw-medium text-primary">{{ $pr->pr_number }}</a></td>
                        <td>{{ $pr->request_date->format('Y-m-d') }}</td>
                        <td>{{ $pr->requester?->name }}</td>
                        <td><span class="badge bg-label-{{ $pr->priority_color }}">{{ $pr->priority_label }}</span></td>
                        <td><span class="badge bg-label-{{ $pr->status_color }}">{{ $pr->status_label }}</span></td>
                        <td>{{ $pr->items_count }}</td>
                        <td>{{ $pr->required_by_date?->format('Y-m-d') ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('warehouse.purchase-requests.show', $pr) }}" class="btn btn-sm btn-icon btn-outline-primary" title="مشاهده"><i class="bx bx-show"></i></a>
                                @if($pr->isEditable())
                                <a href="{{ route('warehouse.purchase-requests.edit', $pr) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش"><i class="bx bx-edit"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4 text-muted">هیچ درخواست خریدی یافت نشد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">{{ $requests->firstItem() ?? 0 }} تا {{ $requests->lastItem() ?? 0 }} از {{ $requests->total() }}</small>
            {{ $requests->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
// ─── Bulk select ───────────────────────────────────────────────────────────
const checkAll  = document.getElementById('check-all');
const rowChecks = () => document.querySelectorAll('.row-check');
const bulkBar   = document.getElementById('bulk-actions');

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked').length;
    if (bulkBar) bulkBar.classList.toggle('d-none', checked === 0);
}
if (checkAll) {
    checkAll.addEventListener('change', () => {
        rowChecks().forEach(c => c.checked = checkAll.checked);
        updateBulkBar();
    });
}
document.addEventListener('change', e => {
    if (e.target.classList.contains('row-check')) {
        checkAll && (checkAll.checked = [...rowChecks()].every(c => c.checked));
        updateBulkBar();
    }
});
</script>
@endpush
@endsection
