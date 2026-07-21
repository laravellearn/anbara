@extends('super-admin.layouts.master')
@section('title', 'مدیریت تعرفه‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0"><i class="bx bx-package text-primary me-2"></i>تعرفه‌ها و پلن‌ها</h4>
    <a href="{{ route('super-admin.plans.create') }}" class="btn btn-primary btn-sm">
      <i class="bx bx-plus me-1"></i> تعرفه جدید
    </a>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>نام پلن</th>
            <th>قیمت ماهانه</th>
            <th>قیمت سالانه</th>
            <th>مدت (روز)</th>
            <th>اشتراک‌ها</th>
            <th>وضعیت</th>
            <th class="text-center">عملیات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($plans as $plan)
          <tr>
            <td class="text-muted small">{{ $plan->sort_order }}</td>
            <td>
              <strong>{{ $plan->name }}</strong>
              <div><code class="small text-muted">{{ $plan->slug }}</code></div>
            </td>
            <td class="fw-semibold">{{ number_format($plan->monthly_price) }} <small class="text-muted">تومان</small></td>
            <td class="text-muted small">{{ number_format($plan->yearly_price) }} تومان</td>
            <td class="text-center">
              {{ $plan->duration_days ? $plan->duration_days . ' روز' : '<span class="text-success small">نامحدود</span>' }}
            </td>
            <td class="text-center">
              <span class="badge bg-label-info">{{ $plan->subscriptions_count }}</span>
            </td>
            <td>
              <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                {{ $plan->is_active ? 'فعال' : 'غیرفعال' }}
              </span>
            </td>
            <td>
              <div class="d-flex gap-1 justify-content-center">
                <a href="{{ route('super-admin.plans.edit', $plan) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش">
                  <i class="bx bx-edit"></i>
                </a>
                <form action="{{ route('super-admin.plans.toggle-status', $plan) }}" method="POST" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-icon btn-outline-{{ $plan->is_active ? 'secondary' : 'success' }}"
                    title="{{ $plan->is_active ? 'غیرفعال' : 'فعال' }}">
                    <i class="bx bx-{{ $plan->is_active ? 'pause' : 'play' }}"></i>
                  </button>
                </form>
                <form action="{{ route('super-admin.plans.destroy', $plan) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('حذف پلن؟')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-icon btn-outline-danger" title="حذف">
                    <i class="bx bx-trash"></i>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-5">هیچ تعرفه‌ای وجود ندارد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
