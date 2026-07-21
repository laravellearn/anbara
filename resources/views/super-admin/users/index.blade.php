@extends('super-admin.layouts.master')
@section('title', 'مدیریت کاربران')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0"><i class="bx bx-group text-primary me-2"></i>مدیریت کاربران</h4>
    <a href="{{ route('super-admin.users.create') }}" class="btn btn-primary btn-sm">
      <i class="bx bx-user-plus me-1"></i> کاربر جدید
    </a>
  </div>

  {{-- Stats --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-4">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-primary">{{ number_format($stats['total']) }}</div>
        <div class="small text-muted">کل کاربران</div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-success">{{ number_format($stats['active']) }}</div>
        <div class="small text-muted">فعال</div>
      </div>
    </div>
    <div class="col-sm-4">
      <div class="card border-0 shadow-sm text-center py-3">
        <div class="fs-2 fw-bold text-danger">{{ number_format($stats['inactive']) }}</div>
        <div class="small text-muted">مسدود</div>
      </div>
    </div>
  </div>

  {{-- Filter --}}
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label small mb-1">جستجو</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="نام / ایمیل / موبایل" value="{{ request('search') }}">
        </div>
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
          <label class="form-label small mb-1">وضعیت</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">همه</option>
            <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>فعال</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>مسدود</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-sm btn-primary"><i class="bx bx-search"></i> جستجو</button>
          <a href="{{ route('super-admin.users.index') }}" class="btn btn-sm btn-outline-secondary">پاک</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>کاربر</th>
            <th>سازمان</th>
            <th>موبایل</th>
            <th>وضعیت</th>
            <th>تاریخ ثبت</th>
            <th class="text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm">
                  <span class="avatar-initial rounded-circle bg-label-info">{{ mb_substr($user->name, 0, 1) }}</span>
                </div>
                <div>
                  <strong class="d-block small">{{ $user->name }}</strong>
                  <span class="text-muted small">{{ $user->email }}</span>
                </div>
              </div>
            </td>
            <td class="small">{{ $user->tenant?->name ?? '—' }}</td>
            <td class="small text-muted">{{ $user->mobile ?? '—' }}</td>
            <td>
              <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                {{ $user->is_active ? 'فعال' : 'مسدود' }}
              </span>
            </td>
            <td class="small text-muted">{{ $user->created_at->toJalali('Y/m/d') }}</td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                <a href="{{ route('super-admin.users.edit', $user) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش">
                  <i class="bx bx-edit"></i>
                </a>
                <form action="{{ route('super-admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-icon btn-outline-{{ $user->is_active ? 'secondary' : 'success' }}"
                    title="{{ $user->is_active ? 'مسدود کردن' : 'فعال کردن' }}">
                    <i class="bx bx-{{ $user->is_active ? 'block' : 'check' }}"></i>
                  </button>
                </form>
                <form action="{{ route('super-admin.impersonate.store') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <button class="btn btn-sm btn-icon btn-outline-primary" title="ورود به‌عنوان کاربر">
                    <i class="bx bx-log-in-circle"></i>
                  </button>
                </form>
                @if(!$user->isSuperAdmin())
                <form action="{{ route('super-admin.users.destroy', $user) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('آیا مطمئن هستید؟')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-icon btn-outline-danger" title="حذف">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted py-5">هیچ کاربری یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $users->links() }}</div>
  </div>

</div>
@endsection
