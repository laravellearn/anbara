@extends('layouts.master')
@section('title', 'تاریخچه اشتراک‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bx bx-history me-2 text-primary"></i>تاریخچه اشتراک‌ها</h4>
    <a href="{{ route('billing.plans') }}" class="btn btn-primary btn-sm">
      <i class="bx bx-plus me-1"></i> ارتقاء / تمدید
    </a>
  </div>

  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show"><i class="bx bx-check-circle me-1"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  <div class="card shadow-none border">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>پلن</th>
            <th>تاریخ شروع</th>
            <th>تاریخ پایان</th>
            <th>پایان دوره آزمایشی</th>
            <th>وضعیت</th>
            <th>مبلغ (ماهانه)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($subscriptions as $sub)
          <tr>
            <td><small class="text-muted">{{ $sub->id }}</small></td>
            <td><span class="fw-medium">{{ $sub->plan?->name ?? '—' }}</span></td>
            <td>{{ verta($sub->starts_at)->format('Y/m/d') }}</td>
            <td>{{ $sub->ends_at ? verta($sub->ends_at)->format('Y/m/d') : '—' }}</td>
            <td>{{ $sub->trial_ends_at ? verta($sub->trial_ends_at)->format('Y/m/d') : '—' }}</td>
            <td>
              @php $statusMap = ['active'=>['success','فعال'],'trial'=>['info','آزمایشی'],'expired'=>['warning','منقضی'],'cancelled'=>['secondary','لغو شده']]; $s=$statusMap[$sub->status]??['secondary',$sub->status]; @endphp
              <span class="badge bg-label-{{ $s[0] }}">{{ $s[1] }}</span>
            </td>
            <td>{{ $sub->plan ? number_format($sub->plan->monthly_price).' ﷼' : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">تاریخچه‌ای یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
      <small class="text-muted">{{ $subscriptions->firstItem() ?? 0 }} تا {{ $subscriptions->lastItem() ?? 0 }} از {{ $subscriptions->total() }}</small>
      {{ $subscriptions->links() }}
    </div>
  </div>
</div>
@endsection