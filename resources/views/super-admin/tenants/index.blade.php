@extends('super-admin.layouts.master')
@section('title', 'مدیریت سازمان‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- ─── Stats ─────────────────────────────────────────────────────────── --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-primary">{{ $stats['total'] }}</div>
        <div class="text-muted small">کل سازمان‌ها</div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-success">{{ $stats['active'] }}</div>
        <div class="text-muted small">فعال</div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-secondary">{{ $stats['inactive'] }}</div>
        <div class="text-muted small">غیرفعال</div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-danger">{{ $stats['expiring'] }}</div>
        <div class="text-muted small">اشتراک در حال انقضا</div>
      </div>
    </div>
  </div>

  {{-- ─── Filter + Actions ──────────────────────────────────────────────── --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
      <form method="GET" action="{{ route('super-admin.tenants.index') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label small mb-1">جستجو</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="نام / ایمیل / slug" value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label small mb-1">وضعیت</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">همه</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>فعال</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غیرفعال</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label small mb-1">پلن</label>
          <select name="plan_id" class="form-select form-select-sm">
            <option value="">همه پلن‌ها</option>
            @foreach($plans as $plan)
            <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-sm btn-primary"><i class="bx bx-search"></i> جستجو</button>
          <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-sm btn-outline-secondary">پاک</a>
          <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-sm btn-success ms-auto">
            <i class="bx bx-plus"></i> جدید
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- ─── Table ──────────────────────────────────────────────────────────── --}}
  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>سازمان</th>
            <th>ایمیل / تلفن</th>
            <th>پلن فعال</th>
            <th>کاربران</th>
            <th>وضعیت</th>
            <th>تاریخ ثبت</th>
            <th class="text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tenants as $tenant)
          <tr>
            <td class="text-muted small">{{ $tenant->id }}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <span class="avatar-initial rounded bg-label-primary">{{ mb_substr($tenant->name, 0, 1) }}</span>
                </div>
                <div>
                  <strong class="d-block">{{ $tenant->name }}</strong>
                  <code class="small text-muted">{{ $tenant->slug }}</code>
                </div>
              </div>
            </td>
            <td class="small">
              <div>{{ $tenant->email ?? '—' }}</div>
              <div class="text-muted">{{ $tenant->phone ?? '' }}</div>
            </td>
            <td>
              @if($tenant->activeSubscription?->plan)
                <span class="badge bg-label-primary">{{ $tenant->activeSubscription->plan->name }}</span>
                @if($tenant->activeSubscription->ends_at)
                  <div class="small text-muted">تا {{ $tenant->activeSubscription->ends_at->toJalali('Y/m/d') }}</div>
                @endif
              @else
                <span class="badge bg-label-secondary">بدون پلن</span>
              @endif
            </td>
            <td class="text-center">
              <span class="badge bg-label-info">{{ $tenant->users_count }}</span>
            </td>
            <td>
              <span class="badge bg-{{ $tenant->is_active ? 'success' : 'danger' }}">
                {{ $tenant->is_active ? 'فعال' : 'غیرفعال' }}
              </span>
            </td>
            <td class="small text-muted">{{ $tenant->created_at->toJalali('Y/m/d') }}</td>
            <td>
              <div class="d-flex gap-1 justify-content-center flex-wrap">
                <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-sm btn-icon btn-outline-info" title="جزئیات">
                  <i class="bx bx-show"></i>
                </a>
                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش">
                  <i class="bx bx-edit"></i>
                </a>
                <form action="{{ route('super-admin.tenants.toggle-status', $tenant) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-icon btn-outline-{{ $tenant->is_active ? 'secondary' : 'success' }}"
                    title="{{ $tenant->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                    <i class="bx bx-{{ $tenant->is_active ? 'pause' : 'play' }}"></i>
                  </button>
                </form>
                {{-- Impersonate: اولین کاربر سازمان --}}
                @php $firstUser = $tenant->users()->first(); @endphp
                @if($firstUser)
                <form action="{{ route('super-admin.impersonate.store') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $firstUser->id }}">
                  <button class="btn btn-sm btn-icon btn-outline-primary" title="ورود به سازمان">
                    <i class="bx bx-log-in-circle"></i>
                  </button>
                </form>
                @endif
                <form action="{{ route('super-admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('آیا مطمئن هستید؟')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-icon btn-outline-danger" title="حذف">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-5">هیچ سازمانی یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $tenants->links() }}
    </div>
  </div>

</div>
@endsection
