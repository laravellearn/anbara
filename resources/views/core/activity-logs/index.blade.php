@extends('layouts.master')

@section('title', 'لاگ فعالیت‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bx bx-history me-2 text-primary"></i>لاگ فعالیت‌ها (Audit Trail)</h4>
  </div>

  {{-- فیلترها --}}
  <div class="card shadow-none border mb-4">
    <div class="card-body py-3">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
          <label class="form-label fw-medium small">کاربر</label>
          <input type="text" name="user" value="{{ request('user') }}" class="form-control form-control-sm" placeholder="نام کاربر...">
        </div>
        <div class="col-sm-2">
          <label class="form-label fw-medium small">عملیات</label>
          <select name="action" class="form-select form-select-sm">
            <option value="">همه</option>
            <option value="created"  @selected(request('action')==='created')>ایجاد</option>
            <option value="updated"  @selected(request('action')==='updated')>ویرایش</option>
            <option value="deleted"  @selected(request('action')==='deleted')>حذف</option>
          </select>
        </div>
        <div class="col-sm-3">
          <label class="form-label fw-medium small">نوع موجودیت</label>
          <input type="text" name="subject_type" value="{{ request('subject_type') }}" class="form-control form-control-sm" placeholder="مثلاً: PurchaseOrder">
        </div>
        <div class="col-sm-2">
          <label class="form-label fw-medium small">از تاریخ</label>
          <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
        </div>
        <div class="col-sm-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="bx bx-search me-1"></i>فیلتر</button>
          <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary btn-sm" title="پاک کردن"><i class="bx bx-reset"></i></a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-none border">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>زمان</th>
            <th>کاربر</th>
            <th>عملیات</th>
            <th>موجودیت</th>
            <th>شناسه</th>
            <th>شرح</th>
            <th>IP</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
          <tr>
            <td><small>{{ verta($log->created_at)->format('Y/m/d H:i') }}</small></td>
            <td>{{ $log->user?->name ?? '—' }}</td>
            <td>
              @php $actionMap = ['created'=>['success','ایجاد'],'updated'=>['info','ویرایش'],'deleted'=>['danger','حذف']]; $a=$actionMap[$log->action]??['secondary',$log->action]; @endphp
              <span class="badge bg-label-{{ $a[0] }}">{{ $a[1] }}</span>
            </td>
            <td><small class="text-muted">{{ class_basename($log->subject_type) }}</small></td>
            <td><small class="text-muted">#{{ $log->subject_id }}</small></td>
            <td>{{ Str::limit($log->description, 60) }}</td>
            <td dir="ltr"><small>{{ $log->ip_address }}</small></td>
            <td>
              @if($log->old_values || $log->new_values)
              <button class="btn btn-sm btn-icon btn-outline-secondary"
                data-bs-toggle="modal" data-bs-target="#logDetailModal"
                data-old="{{ htmlspecialchars(json_encode($log->old_values, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) }}"
                data-new="{{ htmlspecialchars(json_encode($log->new_values, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) }}"
                title="مشاهده تغییرات">
                <i class="bx bx-code-alt"></i>
              </button>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center py-4 text-muted">لاگی یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
      <small class="text-muted">{{ $logs->firstItem() ?? 0 }} تا {{ $logs->lastItem() ?? 0 }} از {{ $logs->total() }}</small>
      {{ $logs->links() }}
    </div>
  </div>
</div>

{{-- مودال تغییرات --}}
<div class="modal fade" id="logDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">جزئیات تغییرات</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <h6 class="text-danger">قبل از تغییر</h6>
          <pre id="old-values" class="bg-light p-3 rounded" style="font-size:11px; max-height:350px; overflow:auto; direction:ltr;"></pre>
        </div>
        <div class="col-md-6">
          <h6 class="text-success">بعد از تغییر</h6>
          <pre id="new-values" class="bg-light p-3 rounded" style="font-size:11px; max-height:350px; overflow:auto; direction:ltr;"></pre>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('logDetailModal').addEventListener('show.bs.modal', function(e) {
  const btn = e.relatedTarget;
  document.getElementById('old-values').textContent = btn.dataset.old || '—';
  document.getElementById('new-values').textContent = btn.dataset.new || '—';
});
</script>
@endpush
@endsection