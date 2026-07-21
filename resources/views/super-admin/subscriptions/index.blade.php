@extends('super-admin.layouts.master')
@section('title', 'مدیریت اشتراک‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0"><i class="bx bx-credit-card text-primary me-2"></i>مدیریت اشتراک‌ها</h4>
    <a href="{{ route('super-admin.subscriptions.create') }}" class="btn btn-primary btn-sm">
      <i class="bx bx-plus me-1"></i> اشتراک جدید
    </a>
  </div>

  {{-- Stats --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-4">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-success">{{ $stats['active'] }}</div>
        <div class="small text-muted">فعال</div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="card border-0 shadow-sm text-center py-3 border-danger border-start border-3">
        <div class="fs-2 fw-bold text-danger">{{ $stats['expiring'] }}</div>
        <div class="small text-muted">در حال انقضا (۳۰ روز)</div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-secondary">{{ $stats['canceled'] }}</div>
        <div class="small text-muted">لغو‌شده</div>
      </div>
    </div>
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
        <div class="col-md-3">
          <label class="form-label small mb-1">پلن</label>
          <select name="plan_id" class="form-select form-select-sm">
            <option value="">همه پلن‌ها</option>
            @foreach($plans as $p)
            <option value="{{ $p->id }}" {{ request('plan_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small mb-1">وضعیت</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">همه</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>فعال</option>
            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>لغو‌شده</option>
            <option value="expired"  {{ request('status') == 'expired'  ? 'selected' : '' }}>منقضی</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small mb-1">انقضای زود</label>
          <div class="form-check mt-1">
            <input type="checkbox" name="expiring" value="1" class="form-check-input" {{ request('expiring') ? 'checked' : '' }}>
            <label class="form-check-label small">در حال انقضا</label>
          </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-sm btn-primary"><i class="bx bx-search"></i></button>
          <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary">پاک</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>سازمان</th><th>پلن</th><th>شروع</th><th>پایان</th><th>وضعیت</th><th class="text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subscriptions as $sub)
          <tr>
            <td>
              <a href="{{ route('super-admin.tenants.show', $sub->tenant_id) }}" class="text-body fw-semibold small">
                {{ $sub->tenant?->name ?? '—' }}
              </a>
            </td>
            <td><span class="badge bg-label-primary">{{ $sub->plan?->name ?? '—' }}</span></td>
            <td class="small text-muted">{{ $sub->starts_at?->toJalali('Y/m/d') ?? '—' }}</td>
            <td class="small">
              @if($sub->ends_at)
                @php $daysLeft = now()->diffInDays($sub->ends_at, false); @endphp
                <span class="{{ $daysLeft < 30 && $daysLeft >= 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                  {{ $sub->ends_at->toJalali('Y/m/d') }}
                  @if($daysLeft >= 0 && $daysLeft < 30)
                    <span class="badge bg-danger ms-1">{{ $daysLeft }} روز</span>
                  @endif
                </span>
              @else
                <span class="text-success">نامحدود</span>
              @endif
            </td>
            <td>
              @php $colors = ['active'=>'success','canceled'=>'danger','expired'=>'secondary']; @endphp
              @php $labels = ['active'=>'فعال','canceled'=>'لغو‌شده','expired'=>'منقضی']; @endphp
              <span class="badge bg-{{ $colors[$sub->status] ?? 'secondary' }}">
                {{ $labels[$sub->status] ?? $sub->status }}
              </span>
            </td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                @if($sub->status == 'active')
                <form action="{{ route('super-admin.subscriptions.renew', $sub) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-icon btn-outline-success" title="تمدید">
                    <i class="bx bx-refresh"></i>
                  </button>
                </form>
                <form action="{{ route('super-admin.subscriptions.cancel', $sub) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('لغو اشتراک؟')">
                  @csrf
                  <button class="btn btn-sm btn-icon btn-outline-danger" title="لغو">
                    <i class="bx bx-x-circle"></i>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-5">هیچ اشتراکی یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $subscriptions->links() }}</div>
  </div>

</div>
@endsection
