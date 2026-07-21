@extends('super-admin.layouts.master')
@section('title', 'لاگ‌های سیستمی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0"><i class="bx bx-history text-secondary me-2"></i>لاگ‌های سیستمی</h4>
    <span class="badge bg-label-info fs-6">امروز: {{ number_format($totalToday) }} رویداد</span>
  </div>

  {{-- Filter --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label small mb-1">سازمان</label>
          <select name="tenant_id" class="form-select form-select-sm">
            <option value="">همه سازمان‌ها</option>
            @foreach($tenants as $t)
            <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small mb-1">عملیات</label>
          <input type="text" name="action" class="form-control form-control-sm" placeholder="مثال: login" value="{{ request('action') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small mb-1">از تاریخ</label>
          <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small mb-1">تا تاریخ</label>
          <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-sm btn-primary"><i class="bx bx-filter"></i> فیلتر</button>
          <a href="{{ route('super-admin.activity-logs.index') }}" class="btn btn-sm btn-outline-secondary">پاک</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 table-sm">
        <thead class="table-light">
          <tr>
            <th>کاربر</th><th>سازمان</th><th>عملیات</th><th>توضیحات</th><th>IP</th><th>تاریخ</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          <tr>
            <td class="small">{{ $log->user?->name ?? '<span class="text-muted">سیستم</span>' }}</td>
            <td class="small text-muted">{{ $log->tenant?->name ?? '—' }}</td>
            <td><span class="badge bg-label-secondary">{{ $log->action }}</span></td>
            <td class="small text-muted" style="max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              {{ $log->description ?? '—' }}
            </td>
            <td class="small text-muted">{{ $log->ip_address ?? '—' }}</td>
            <td class="small text-muted">{{ $log->created_at->toJalali('Y/m/d H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-5">لاگی ثبت نشده است.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $logs->links() }}</div>
  </div>

</div>
@endsection
